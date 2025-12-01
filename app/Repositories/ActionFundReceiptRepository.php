<?php

namespace App\Repositories;

use App\Helpers\ReferenceGenerator;
use App\Http\Requests\ActionFundReceiptRequest;
use App\Http\Resources\ActionFundReceiptResource;
use App\Models\Action;
use App\Models\ActionFundReceipt;
use App\Models\Currency;
use App\Models\FundingSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Facades\Auth;

class ActionFundReceiptRepository
{
    /**
     * List actions with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['reference', 'funding_source', 'action'];
        $sortable = ['reference', 'action', 'receipt_date', 'validity_date', 'amount_original', 'converted_amount', 'funding_source'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = ActionFundReceipt::join('actions', 'action_fund_receipts.action_uuid', '=', 'actions.uuid')
            ->join('funding_sources', 'action_fund_receipts.funding_source_uuid', '=', 'funding_sources.uuid')
            ->select(
                'action_fund_receipts.id',
                'action_fund_receipts.reference',
                'action_fund_receipts.receipt_date',
                'action_fund_receipts.validity_date',
                'actions.currency',
                'action_fund_receipts.exchange_rate',
                'action_fund_receipts.amount_original',
                'action_fund_receipts.converted_amount',
                'funding_sources.name as funding_source',
                'actions.reference as action_reference',
                'actions.name as action_name',
                'actions.id as action_id',
            );

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'funding_source') {
                        $q->orWhere('funding_sources.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } elseif ($column === 'action') {
                        $q->orWhere('actions.reference', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere("action_fund_receipts.$column", 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'funding_source') {
            $query->orderBy('funding_sources.name', $sortOrder);
        } elseif ($sortBy === 'action') {
            $query->orderBy('actions.reference', $sortOrder);
        } else {
            $query->orderBy("action_fund_receipts.$sortBy", $sortOrder);
        }

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }


    /**
     * Load requirements data
     */
    public function requirements(Request $request)
    {
        $mode = $request->get('mode', 'create');

        $fundingSources = FundingSource::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name')
            ->get();

        $currencies = Currency::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'code', 'is_default')
            ->get();

        if ($mode === 'create') {
            $actions = Action::where('status', 'in_progress')
                ->orderBy('id', 'desc')
                ->select('uuid', 'name', 'reference', 'currency')
                ->get();

            return [
                'actions' => $actions,
                'funding_sources' => $fundingSources,
                'currencies' => $currencies,
            ];
        }

        return [
            'funding_sources' => $fundingSources,
            'currencies' => $currencies,
        ];
    }


    /**
     * Store a new actions fund receipt.
     */
    public function store(ActionFundReceiptRequest $request)
    {
        $request->merge([
            'action_uuid' => $request->input('action'),
            'currency_uuid' => $request->input('currency'),
            'funding_source_uuid' => $request->input('funding_source'),
            'created_by' => Auth::user()?->uuid,
            'updated_by' => Auth::user()?->uuid,
        ]);

        $actionFundReceipt = ActionFundReceipt::create($request->only([
            'action_uuid',
            'funding_source_uuid',
            'currency_uuid',
            'exchange_rate',
            'amount_original',
            'converted_amount',
            'receipt_date',
            'validity_date',
            'created_by',
            'updated_by',
        ]));

        $action = $actionFundReceipt->action;
        $actionFundReceipt->update([
            'reference' => ReferenceGenerator::generateFundReceiptReference($actionFundReceipt->id, $action),
        ]);

        $this->updateActionTotalReceiptFund($actionFundReceipt->action_uuid);

        $actionFundReceipt->load([
            'action',
            'fundingSource',
            'currency'
        ]);

        return (new ActionFundReceiptResource($actionFundReceipt))->additional([
            'mode' => $request->input('mode', 'view')
        ]);
    }


    /**
     * Show a specific action fund receipt.
     */
    public function show(ActionFundReceipt $actionFundReceipt)
    {
        return ['action_fund_receipt' => new ActionFundReceiptResource($actionFundReceipt->load([
            'action',
            'fundingSource',
            'currency',
        ]))];
    }

    /**
     * Update an action fund receipt.
     */
    public function update(Request $request, ActionFundReceipt $actionFundReceipt)
    {        
        $request->merge([
            'funding_source_uuid' => $request->input('funding_source'),
            'currency_uuid' => $request->input('currency'),
            'updated_by' => Auth::user()?->uuid,
        ]);

        $actionFundReceipt->fill($request->only([
            'funding_source_uuid',
            'currency_uuid',
            'exchange_rate',
            'amount_original',
            'converted_amount',
            'receipt_date',
            'validity_date',
            'updated_by',
        ]));

        $actionFundReceipt->save();

        $this->updateActionTotalReceiptFund($actionFundReceipt->action_uuid);

        $actionFundReceipt->load([
            'action',
            'fundingSource',
            'currency'
        ]);


        return (new ActionFundReceiptResource($actionFundReceipt))->additional([
            'mode' => $request->input('mode', 'edit')
        ]);
    }

    /**
     * Delete action(s).
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $receipts = ActionFundReceipt::whereIn('id', $ids)->get();

            if ($receipts->isEmpty()) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $actionUuids = $receipts->pluck('action_uuid')->unique();

            $deleted = ActionFundReceipt::whereIn('id', $ids)->delete();
            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            foreach ($actionUuids as $uuid) {
                $this->updateActionTotalReceiptFund($uuid);
            }

            DB::commit();
        } catch (RuntimeException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e->getCode() === "23000") {
                throw new \Exception(__('app/common.repository.foreignKey'));
            }

            throw new \Exception(__('app/common.repository.error'));
        }
    }

    /**
     * Recalculate and update the action's total received funds.
     */
    private function updateActionTotalReceiptFund(string $actionUuid): void
    {
        $total = ActionFundReceipt::where('action_uuid', $actionUuid)->sum('converted_amount');

        Action::where('uuid', $actionUuid)->update([
            'total_receipt_fund' => $total,
        ]);
    }
}
