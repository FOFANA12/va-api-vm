<?php

namespace App\Services\Exports;

use App\Helpers\DateTimeFormatter;
use App\Models\ActionPlan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ActionPlanExportService
{
    protected string $exportPath;

    public function __construct()
    {
        $this->exportPath = public_path('storage/exports/tmp');
    }

    /**
     * Export all action plans
     */
    public function exportAll()
    {
        $structure = Auth::user()?->employee?->structure;

        $query = ActionPlan::with([
            'structure',
            'responsible',
        ])->orderBy('created_at');

        if ($structure) {
            $query->where('structure_uuid', $structure->uuid);
        }

        $actionPlans = $query->get();

        $filename = __('app/action_plan.export.filename_all');

        return $this->generateExcel($actionPlans, $filename);
    }

    /**
     * Generate Excel file
     */
    private function generateExcel($actionPlans, string $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(__('app/action_plan.export.sheet_name'));

        $headers = [
            __('app/action_plan.export.structure_abbreviation'),
            __('app/action_plan.export.structure_label'),
            __('app/action_plan.export.reference'),
            __('app/action_plan.export.name'),
            __('app/action_plan.export.description'),
            __('app/action_plan.export.responsible'),
            __('app/action_plan.export.start_date'),
            __('app/action_plan.export.end_date'),
            __('app/action_plan.export.created_at'),
            __('app/action_plan.export.updated_at'),
        ];

        $borderStyle = [
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        // Headers
        foreach ($headers as $i => $header) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $cell = $col . '1';

            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => $borderStyle['borders'],
            ]);
        }

        $row = 2;

        foreach ($actionPlans as $actionPlan) {
            $data = [
                $actionPlan->structure->abbreviation,
                $actionPlan->structure->name,
                $actionPlan->reference,
                $actionPlan->name,
                $actionPlan->description ?? '-',
                $actionPlan->responsible?->email ?? '-',
                $actionPlan->start_date
                    ? DateTimeFormatter::formatDatetime($actionPlan->start_date)
                    : '-',
                $actionPlan->end_date
                    ? DateTimeFormatter::formatDatetime($actionPlan->end_date)
                    : '-',
                $actionPlan->created_at
                    ? DateTimeFormatter::formatDatetime($actionPlan->created_at)
                    : '-',
                $actionPlan->updated_at
                    ? DateTimeFormatter::formatDatetime($actionPlan->updated_at)
                    : '-',
            ];

            foreach ($data as $i => $value) {
                $col = Coordinate::stringFromColumnIndex($i + 1);
                $cell = $col . $row;

                $sheet->setCellValueExplicit(
                    $cell,
                    (string) $value,
                    DataType::TYPE_STRING
                );

                $sheet->getStyle($cell)->applyFromArray($borderStyle);
            }

            $row++;
        }

        // Auto-size columns
        for ($i = 1; $i <= count($headers); $i++) {
            $col = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // File system
        $fs = new Filesystem();

        if (!$fs->exists($this->exportPath)) {
            $fs->makeDirectory($this->exportPath, 0755, true);
        }

        // Clean old exports
        foreach ($fs->files($this->exportPath) as $file) {
            $fs->delete($file);
        }

        $filePath = "{$this->exportPath}/{$filename}";
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(false);
    }
}
