<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use App\Helpers\ReferenceGenerator;
use App\Http\Requests\ActionFundDisbursementRequest;
use App\Http\Resources\ActionFundDisbursementResource;
use App\Models\Action;
use App\Models\ActionFundDisbursement;
use App\Models\BudgetType;
use App\Models\ExpenseType;
use App\Models\PaymentMode;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Facades\Auth;

class ActionFundDisbursementRepository
{
    /**
     * List disbursements with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['reference', 'action', 'contract', 'operation_number', 'supplier', 'phase', 'task', 'payment_mode'];
        $sortable = ['reference', 'action_name', 'contract', 'operation_number', 'supplier', 'payment_amount', 'execution_date', 'task'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = ActionFundDisbursement::join('actions', 'action_fund_disbursements.action_uuid', '=', 'actions.uuid')
            ->leftJoin('action_phases', 'action_fund_disbursements.phase_uuid', '=', 'action_phases.uuid')
            ->leftJoin('tasks', 'action_fund_disbursements.task_uuid', '=', 'tasks.uuid')
            ->leftJoin('payment_modes', 'action_fund_disbursements.payment_mode_uuid', '=', 'payment_modes.uuid')
            ->join('contracts', 'action_fund_disbursements.contract_uuid', '=', 'contracts.uuid')
            ->join('suppliers', 'action_fund_disbursements.supplier_uuid', '=', 'suppliers.uuid')
            ->leftJoin('attachments', function ($join) {
                $join->on('attachments.attachable_id', '=', 'action_fund_disbursements.id')
                    ->where('attachments.attachable_type', '=', ActionFundDisbursement::tableName());
            })
            ->select(
                'action_fund_disbursements.id',
                'action_fund_disbursements.uuid',
                'action_fund_disbursements.reference',
                'action_fund_disbursements.operation_number',
                'action_fund_disbursements.payment_amount',
                'action_fund_disbursements.execution_date',
                'action_fund_disbursements.payment_date',
                'actions.reference as action_reference',
                'actions.currency',
                'actions.name as action_name',
                'actions.id as action_id',

                'action_phases.name as phase',
                'tasks.title as task',
                'payment_modes.name as payment_mode',

                'contracts.contract_number',
                'suppliers.company_name as supplier',
                'attachments.id as attachment_id',
            );

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'action') {
                        $q->orWhere('actions.reference', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else if ($column === 'phase') {
                        $q->orWhere('action_phases.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else if ($column === 'task') {
                        $q->orWhere('tasks.title', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else if ($column === 'payment_mode') {
                        $q->orWhere('payment_modes.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else if ($column === 'contract') {
                        $q->orWhere('contracts.contract_number', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else if ($column === 'supplier') {
                        $q->orWhere('suppliers.company_name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere("action_fund_disbursements.$column", 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'action') {
            $query->orderBy('actions.reference', $sortOrder);
        } else if ($sortBy === 'phase') {
            $query->orderBy('action_phases.name', $sortOrder);
        } else if ($sortBy === 'task') {
            $query->orderBy('tasks.title', $sortOrder);
        } else if ($sortBy === 'payment_mode') {
            $query->orderBy('payment_modes.name', $sortOrder);
        } else if ($sortBy === 'contract') {
            $query->orderBy('contracts.contract_number', $sortOrder);
        } else if ($sortBy === 'supplier') {
            $query->orderBy('suppliers.company_name', $sortOrder);
        } else {
            $query->orderBy("action_fund_disbursements.$sortBy", $sortOrder);
        }

        $query->orderBy($sortBy, $sortOrder);

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

        $paymentModes = PaymentMode::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name')
            ->get();


        $budgetTypes = BudgetType::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name')
            ->get();

        $expenseTypes = ExpenseType::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name')
            ->get();

        $suppliers = Supplier::where('status', true)
            ->with(['contracts' => function ($query) {
                $query->where('status', true)
                    ->select('uuid', 'contract_number', 'supplier_uuid', 'signed_at');
            }])
            ->orderByDesc('note')
            ->select('uuid', 'company_name', 'tax_number', 'note', 'contract_type_uuid')
            ->get();

        if ($mode === 'create') {
            $actions = Action::with([
                'phases' => function ($q) {
                    $q->orderBy('number', 'asc')
                        ->select('id', 'uuid', 'action_uuid', 'name', 'number')
                        ->with(['tasks' => function ($taskQuery) {
                            $taskQuery->orderBy('start_date', 'asc')
                                ->select('id', 'uuid', 'phase_uuid', 'title', 'start_date', 'end_date');
                        }]);
                }
            ])->where('status', 'in_progress')
                ->orderBy('id', 'desc')
                ->select('uuid', 'name', 'reference', 'currency', 'contract_type_uuid')
                ->get();

            return [
                'actions' => $actions,
                'payment_modes' => $paymentModes,
                'budget_types' => $budgetTypes,
                'expense_types' => $expenseTypes,
                'suppliers' => $suppliers,
            ];
        }

        return [
            'payment_modes' => $paymentModes,
            'budget_types' => $budgetTypes,
            'expense_types' => $expenseTypes,
            'suppliers' => $suppliers,
        ];
    }

    /**
     * Store a new disbursement.
     */
    public function store(ActionFundDisbursementRequest $request)
    {
        $identifier = null;
        DB::beginTransaction();
        try {
            $request->merge([
                'action_uuid' => $request->input('action'),
                'payment_mode_uuid' => $request->input('payment_mode'),
                'budget_type_uuid' => $request->input('budget_type'),
                'phase_uuid' => $request->input('phase'),
                'task_uuid' => $request->input('task'),

                'supplier_uuid' => $request->input('supplier'),
                'contract_uuid' => $request->input('contract'),

                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $actionFundDisbursement = ActionFundDisbursement::create($request->only([
                'action_uuid',
                'operation_number',
                'signature_date',
                'execution_date',
                'payment_date',
                'payment_amount',
                'payment_mode_uuid',
                'cheque_reference',
                'budget_type_uuid',
                'phase_uuid',
                'task_uuid',
                'supplier_uuid',
                'contract_uuid',
                'description',
                'created_by',
                'updated_by'
            ]));

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $identifier = FileHelper::upload($file, 'uploads');

                $actionFundDisbursement->attachment()->create([
                    'title' => $request->input('title', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'identifier' => $identifier,
                    'size' => $file->getSize(),
                    'uploaded_by' => Auth::user()->uuid,
                    'uploaded_at' => now(),
                ]);
            }

            $action = $actionFundDisbursement->action;
            $actionFundDisbursement->update([
                'reference' => ReferenceGenerator::generateActionFundDisbursementReference($actionFundDisbursement, $action),
            ]);

            $expenseTypesUuids = collect($request->expense_types)
                ->pluck('uuid')
                ->filter()
                ->toArray();
            $validExpenseTypes = ExpenseType::whereIn('uuid', $expenseTypesUuids)
                ->pluck('uuid')
                ->toArray();
            $actionFundDisbursement->expenseTypes()->sync($validExpenseTypes);

            $this->updateActionTotalDisbursementFund($actionFundDisbursement->action_uuid);

            $actionFundDisbursement->load([
                'action.phases',
                'paymentMode',
                'phase',
                'task',
                'budgetType',
                'expenseTypes',
                'attachment'
            ]);

            DB::commit();

            return (new ActionFundDisbursementResource($actionFundDisbursement))->additional([
                'mode' => $request->input('mode', 'view')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            if (!empty($identifier)) {
                FileHelper::delete("uploads/{$identifier}");
            }
            throw $e;
        }
    }

    /**
     * Show a specific disbursement.
     */
    public function show(ActionFundDisbursement $actionFundDisbursement)
    {
        return ['disbursement' => new ActionFundDisbursementResource($actionFundDisbursement->load([
            'action.phases',
            'action.phases.tasks',
            'paymentMode',
            'phase',
            'task',
            'supplier',
            'contract',
            'budgetType',
            'expenseTypes',
            'attachment'
        ]))];
    }

    /**
     * Update an disbursement.
     */
    public function update(ActionFundDisbursementRequest $request, ActionFundDisbursement $actionFundDisbursement)
    {
        $oldFile = $actionFundDisbursement->attachment?->identifier;
        $newFile = null;

        DB::beginTransaction();
        try {
            $request->merge([
                'payment_mode_uuid' => $request->input('payment_mode'),
                'budget_type_uuid' => $request->input('budget_type'),
                'phase_uuid' => $request->input('phase'),
                'task_uuid' => $request->input('task'),
                'supplier_uuid' => $request->input('supplier'),
                'contract_uuid' => $request->input('contract'),
                'updated_by' => Auth::user()?->uuid,
            ]);

            $actionFundDisbursement->fill($request->only([
                'operation_number',
                'signature_date',
                'execution_date',
                'payment_date',
                'payment_amount',
                'payment_mode_uuid',
                'cheque_reference',
                'budget_type_uuid',
                'phase_uuid',
                'task_uuid',

                'supplier_uuid',
                'contract_uuid',
                'description',
                'updated_by'
            ]));

            $actionFundDisbursement->save();

            if ($request->boolean('delete_file') && $oldFile) {
                FileHelper::delete("uploads/{$oldFile}");
                $actionFundDisbursement->attachment()->delete();
            }

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $newFile = FileHelper::upload($file, 'uploads');

                if ($oldFile && $oldFile !== $newFile) {
                    FileHelper::delete("uploads/{$oldFile}");
                    $actionFundDisbursement->attachment()->delete();
                }

                $actionFundDisbursement->attachment()->create([
                    'title' => $request->input('title', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'identifier' => $newFile,
                    'size' => $file->getSize(),
                    'uploaded_by' => Auth::user()->uuid,
                    'uploaded_at' => now(),
                ]);
            }

            $expenseTypesUuids = collect($request->expense_types)
                ->pluck('uuid')
                ->filter()
                ->toArray();
            $validExpenseTypes = ExpenseType::whereIn('uuid', $expenseTypesUuids)
                ->pluck('uuid')
                ->toArray();
            $actionFundDisbursement->expenseTypes()->sync($validExpenseTypes);

            $this->updateActionTotalDisbursementFund($actionFundDisbursement->action_uuid);

            $actionFundDisbursement->load([
                'action.phases',
                'paymentMode',
                'phase',
                'task',
                'budgetType',
                'expenseTypes',
                'attachment'
            ]);

            DB::commit();

            return (new ActionFundDisbursementResource($actionFundDisbursement))->additional([
                'mode' => $request->input('mode', 'edit')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            if (!empty($newFile)) {
                FileHelper::delete("uploads/{$newFile}");
            }
            throw $e;
        }
    }

    /**
     * Delete disbursement(s).
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        $filesToDelete = [];
        DB::beginTransaction();
        try {

            $disbursements = ActionFundDisbursement::with('attachment')->whereIn('id', $ids)->get();

            if ($disbursements->isEmpty()) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            foreach ($disbursements as $disbursement) {
                if ($disbursement->attachment) {
                    $filesToDelete[] = "uploads/{$disbursement->attachment->identifier}";
                    $disbursement->attachment->delete();
                }
                $disbursement->delete();
            }

            DB::commit();

            foreach ($filesToDelete as $filePath) {
                FileHelper::delete($filePath);
            }
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
     * Recalculate and update the action's total disbursed funds.
     */
    private function updateActionTotalDisbursementFund(string $actionUuid): void
    {
        $total = ActionFundDisbursement::where('action_uuid', $actionUuid)->sum('payment_amount');

        Action::where('uuid', $actionUuid)->update([
            'total_disbursement_fund' => $total,
        ]);
    }
}
