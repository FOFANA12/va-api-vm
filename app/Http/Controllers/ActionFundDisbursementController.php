<?php

namespace App\Http\Controllers;

use App\Helpers\DateTimeFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ActionFundDisbursementRequest;
use App\Http\Resources\ActionFundDisbursementResource;
use App\Models\ActionFundDisbursement;
use App\Repositories\ActionFundDisbursementRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Filesystem\Filesystem;
use PhpOffice\PhpWord\TemplateProcessor;

class ActionFundDisbursementController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ActionFundDisbursementRepository $repository)
    {
        $this->messageSuccessCreated = __('app/action_fund_disbursement.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/action_fund_disbursement.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the disbursements.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);

        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, ActionFundDisbursementResource::class)
                ->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, ActionFundDisbursementResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for disbursement.
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created disbursement.
     */
    public function store(ActionFundDisbursementRequest $request)
    {
        $actionFundDisbursement = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'action_fund_disbursement' => $actionFundDisbursement
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified disbursement.
     */
    public function show(ActionFundDisbursement $actionFundDisbursement)
    {
        return response()->json(
            $this->repository->show($actionFundDisbursement)
        )->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified action fund disbursement.
     */
    public function update(ActionFundDisbursementRequest $request, ActionFundDisbursement $actionFundDisbursement)
    {
        $actionFundDisbursement = $this->repository->update($request, $actionFundDisbursement);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'disbursement' => $actionFundDisbursement
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified disbursement(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }

    public function exportToWord(ActionFundDisbursement $actionFundDisbursement)
    {
        $template =  new TemplateProcessor(public_path('storage/templates/fund-disbursement.docx'));

        $structure = $actionFundDisbursement->action->structure;
        $structureName = $structure->name . '(' . $structure->abbreviation . ')';

        if ($structure->parent) {
            $parentStructureName = $structure->parent->name . ' (' . $structure->parent->abbreviation . ')';
            $template->setValue('parentStructure', $parentStructureName);
        } else {
            $template->setValue('parentStructure', "");
        }

        $template->setValue('structure', $structureName);
        $template->setValue('action', $actionFundDisbursement->action->name);
        $template->setValue('supplier', $actionFundDisbursement->supplier->company_name);
        $template->setValue('taxNumber', $actionFundDisbursement->supplier->tax_number);
        $template->setValue('contractNumber', $actionFundDisbursement->contract->contract_number);
        $template->setValue('signatureDate', DateTimeFormatter::formatDate($actionFundDisbursement->signature_date));
        $template->setValue('executionDate', DateTimeFormatter::formatDate($actionFundDisbursement->execution_date));
        $template->setValue('paymentDate', DateTimeFormatter::formatDate($actionFundDisbursement->payment_date));
        $template->setValue('paymentMode', $actionFundDisbursement->paymentMode->name);
        $template->setValue('phase', $actionFundDisbursement->phase ? $actionFundDisbursement->phase?->name : '-');
        $template->setValue('opNumber', $actionFundDisbursement->operation_number);
        $template->setValue('amount', $actionFundDisbursement->payment_amount);
        $template->setValue('chequeReference', $actionFundDisbursement->cheque_reference);
        $template->setValue('budgetType', $actionFundDisbursement->budgetType->name);

        $expenseTypes = $actionFundDisbursement->expenseTypes->pluck('name')->implode(",");
        $template->setValue('expenseTypes', $expenseTypes ? $expenseTypes : '-');

        $template->setValue('currency', $actionFundDisbursement->action->currency);
        $template->setValue('date', DateTimeFormatter::formatDate(now()));

        $exportDir = public_path("storage/templates/export");
        $fileSystem = new Filesystem();

        if (!$fileSystem->exists($exportDir)) {
            $fileSystem->makeDirectory($exportDir, 0755, true);
        } else {
            $fileSystem->cleanDirectory($exportDir);
        }

        $filePath = $exportDir . "/{$actionFundDisbursement->reference}.docx";
        $template->saveAs($filePath);

        return response()->download($filePath);
    }
}
