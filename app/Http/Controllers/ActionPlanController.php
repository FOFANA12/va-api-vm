<?php

namespace App\Http\Controllers;

use App\Helpers\DateTimeFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ActionPlanImportRequest;
use App\Http\Requests\ActionPlanRequest;
use App\Http\Resources\ActionPlanResource;
use App\Models\ActionPlan;
use App\Repositories\ActionPlanRepository;
use App\Services\Exports\ActionPlanExportService;
use App\Services\Imports\ActionPlanImportService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class ActionPlanController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessDuplicated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private string $messageImportSuccess;
    private string $messageImportFailed;
    private $repository;
    private ActionPlanExportService $exportService;

    public function __construct(ActionPlanRepository $repository, ActionPlanExportService $exportService)
    {
        $this->messageSuccessCreated = __('app/action_plan.controller.message_success_created');
        $this->messageSuccessDuplicated = __('app/action_plan.controller.message_success_duplicated');
        $this->messageSuccessUpdated = __('app/action_plan.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');

        $this->messageImportSuccess = __('app/action_plan.import.success');
        $this->messageImportFailed = __('app/action_plan.import.failed');

        $this->repository = $repository;
        $this->exportService = $exportService;
    }

    /**
     * Display a listing of the action plans.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);

        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, ActionPlanResource::class)
                ->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, ActionPlanResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for action plan.
     */
    public function requirements()
    {
        return response()->json($this->repository->requirements())
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created action plan.
     */
    public function store(ActionPlanRequest $request)
    {
        $actionPlan = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'action_plan' => $actionPlan
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Duplicate an existing Action Plan along with its Actions.
     */
    public function duplicate(ActionPlanRequest $request)
    {
        $newPlan = $this->repository->duplicate($request);

        return response()->json([
            'message' => $this->messageSuccessDuplicated,
            'action_plan' => $newPlan
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified action plan.
     */
    public function show(ActionPlan $actionPlan)
    {
        return response()->json(
            $this->repository->show($actionPlan)
        )->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified action plan.
     */
    public function update(ActionPlanRequest $request, ActionPlan $actionPlan)
    {
        $actionPlan = $this->repository->update($request, $actionPlan);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'action_plan' => $actionPlan
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified action plan(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }

    public function exportToExcel(ActionPlan $actionPlan)
    {
        $spreadsheet = IOFactory::load(public_path('storage/templates/template-ap.xlsx'));
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle(\Illuminate\Support\Str::limit($actionPlan->reference, 25));

        $structure = $actionPlan->structure;
        $parentStructure = $structure->parent;

        $parentStructureName = $parentStructure ? $parentStructure->name . '(' . $parentStructure->abbreviation . ')' : "-";
        $structureName = $structure->name . '(' . $structure->abbreviation . ')';

        $worksheet->setCellValue('A2', $parentStructureName);
        $worksheet->setCellValue('A3', $structureName);

        $styleArray = [
            'font' => ['bold' => false, 'size' => 11, 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];

        $startLine = 6;
        foreach ($actionPlan->actions as $index => $action) {
            $worksheet->setCellValue('A' . $startLine, $index + 1);
            $worksheet->getStyle('A' . $startLine)->applyFromArray($styleArray);

            $worksheet->setCellValue('B' . $startLine, $action->reference);
            $worksheet->getStyle('B' . $startLine)->applyFromArray($styleArray);

            $worksheet->setCellValue('C' . $startLine, $action->name);
            $worksheet->getStyle('C' . $startLine)->applyFromArray($styleArray);

            $worksheet->setCellValue('D' . $startLine, $action->description);
            $worksheet->getStyle('D' . $startLine)->applyFromArray($styleArray);

            $worksheet->setCellValue('E' . $startLine, DateTimeFormatter::formatDate($action->start_date));
            $worksheet->getStyle('E' . $startLine)->applyFromArray($styleArray);

            $worksheet->setCellValue('F' . $startLine, DateTimeFormatter::formatDate($action->end_date));
            $worksheet->getStyle('F' . $startLine)->applyFromArray($styleArray);

            $worksheet->setCellValue('G' . $startLine, $action->actionDomain?->name);
            $worksheet->getStyle('G' . $startLine)->applyFromArray($styleArray);

            $worksheet->setCellValue('H' . $startLine,  $action->strategicDomain?->name);
            $worksheet->getStyle('H' . $startLine)->applyFromArray($styleArray);

            $worksheet->setCellValue('I' . $startLine, $action->capabilityDomain?->name);
            $worksheet->getStyle('I' . $startLine)->applyFromArray($styleArray);

            $worksheet->setCellValue('J' . $startLine, $action->elementaryLevel?->name);
            $worksheet->getStyle('J' . $startLine)->applyFromArray($styleArray);

            $objectives = $action->objectives->pluck('name')->implode(', ');
            $worksheet->setCellValue("K{$startLine}", $objectives);
            $worksheet->getStyle("K{$startLine}")->applyFromArray($styleArray);
            $worksheet->getStyle("K{$startLine}")->getAlignment()->setWrapText(true);

            $stakeholders = $action->stakeholders->pluck('name')->implode(",\n");
            $worksheet->setCellValue("L{$startLine}", $stakeholders);
            $worksheet->getStyle("L{$startLine}")->applyFromArray($styleArray);

            $worksheet->setCellValue("M{$startLine}", $action->total_budget);
            $worksheet->getStyle("M{$startLine}")->applyFromArray($styleArray);

            $fundingSources = $action->fundingSources->pluck('name')->implode(",\n");
            $worksheet->setCellValue("N{$startLine}", $fundingSources);
            $worksheet->getStyle("N{$startLine}")->applyFromArray($styleArray);

            $localisation = '';
            if ($action->region || $action->department || $action->municipality) {
                $localisation .= 'Région : '      . ($action->region?->name ?? '');
                $localisation .= "\nDépartement : " . ($action->department?->name ?? '');
                $localisation .= "\nCommune : "     . ($action->municipality?->name ?? '');
            }
            $worksheet->setCellValue("O{$startLine}", $localisation);
            $worksheet->getStyle("O{$startLine}")->applyFromArray($styleArray);
            $worksheet->getStyle("O{$startLine}")->getAlignment()->setWrapText(true);

            $beneficiaries = $action->beneficiaries->pluck('name')->implode(",\n");
            $worksheet->setCellValue("P{$startLine}", $beneficiaries);
            $worksheet->getStyle("P{$startLine}")->applyFromArray($styleArray);

            $worksheet->setCellValue("Q{$startLine}", $action->actual_progress_percent);
            $worksheet->getStyle("Q{$startLine}")->applyFromArray($styleArray);

            ++$startLine;
        }

        foreach (range('A', 'Q') as $col) {
            $worksheet->getStyle($col)->getAlignment()->setWrapText(true);
        }

        $exportDir = public_path("storage/templates/export");
        $fileSystem = new Filesystem();

        if (!$fileSystem->exists($exportDir)) {
            $fileSystem->makeDirectory($exportDir, 0755, true);
        } else {
            $fileSystem->cleanDirectory($exportDir);
        }

        $filePath = "{$exportDir}/{$actionPlan->reference}.xlsx";
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);

        return response()->download($filePath);
    }

    /**
     * Import action plans from Excel / CSV file.
     */
    public function import(ActionPlanImportRequest $request, ActionPlanImportService $importService)
    {
        // Options (checkbox frontend)
        $updateIfExists = filter_var(
            $request->input('update_if_exists'),
            FILTER_VALIDATE_BOOLEAN
        );

        // Store file temporarily
        $path = $request->file('import_file')->store('imports/tmp');

        $result = $importService->import(
            Storage::path($path),
            $updateIfExists
        );

        // Optional: cleanup file
        Storage::delete($path);

        if (!$result['success']) {
            return response()->json([
                'message' => $this->messageImportFailed,
                'errors'  => $result['errors'],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'message'  => $this->messageImportSuccess,
            'imported' => $result['imported'],
        ], Response::HTTP_OK);
    }

    /**
     * Mass export (all accounts)
     */
    public function exportAll()
    {
        return $this->exportService->exportAll();
    }
}
