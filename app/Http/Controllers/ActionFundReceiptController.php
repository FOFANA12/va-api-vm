<?php

namespace App\Http\Controllers;

use App\Helpers\DateTimeFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ActionFundReceiptRequest;
use App\Http\Resources\ActionFundReceiptResource;
use App\Models\ActionFundReceipt;
use App\Repositories\ActionFundReceiptRepository;
use App\Support\Currency as SupportCurrency;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Filesystem\Filesystem;
use PhpOffice\PhpWord\TemplateProcessor;

class ActionFundReceiptController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ActionFundReceiptRepository $repository)
    {
        $this->messageSuccessCreated = __('app/action_fund_receipt.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/action_fund_receipt.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a paginated or full list of fund receipts.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, ActionFundReceiptResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, ActionFundReceiptResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Load requirements data
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a new fund receipt.
     */
    public function store(ActionFundReceiptRequest $request)
    {
        $actionFundReceipt = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'action_fund_receipt' => $actionFundReceipt
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the details of a fund receipt.
     */
    public function show(ActionFundReceipt $actionFundReceipt)
    {
        return response()->json($this->repository->show($actionFundReceipt))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update an existing fund receipt.
     */
    public function update(ActionFundReceiptRequest $request, ActionFundReceipt $actionFundReceipt)
    {
        $actionFundReceipt = $this->repository->update($request, $actionFundReceipt);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'action_fund_receipt' => $actionFundReceipt
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Delete one or multiple fund receipts.
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }

    public function exportToWord(ActionFundReceipt $actionFundReceipt)
    {
        $template =  new TemplateProcessor(public_path('storage/templates/fund-receipt.docx'));

        $structure = $actionFundReceipt->action->structure;
        $structureName = $structure->name . '(' . $structure->abbreviation . ')';

        if ($structure->parent) {
            $parentStructureName = $structure->parent->name . ' (' . $structure->parent->abbreviation . ')';
            $template->setValue('parentStructure', $parentStructureName);
        } else {
            $template->setValue('parentStructure', "");
        }

        $template->setValue('structure', $structureName);
        $template->setValue('action', $actionFundReceipt->action->name);
        $template->setValue('receiptDate', DateTimeFormatter::formatDate($actionFundReceipt->receipt_date));
        $template->setValue('source', $actionFundReceipt->fundingSource->name);

        $template->setValue('amountOriginal', $actionFundReceipt->amount_original);
        $template->setValue('currency', $actionFundReceipt->currency->code);

        $systemCurrency = SupportCurrency::getDefault();
        $template->setValue('convertedAmount', $actionFundReceipt->converted_amount);
        $template->setValue('systemCurrency', $systemCurrency['code']);

        $template->setValue('date', DateTimeFormatter::formatDate(now()));

        $exportDir = public_path("storage/templates/export");
        $fileSystem = new Filesystem();

        if (!$fileSystem->exists($exportDir)) {
            $fileSystem->makeDirectory($exportDir, 0755, true);
        } else {
            $fileSystem->cleanDirectory($exportDir);
        }

        $filePath = $exportDir . "/{$actionFundReceipt->reference}.docx";
        $template->saveAs($filePath);

        return response()->download($filePath);
    }
}
