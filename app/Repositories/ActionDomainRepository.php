<?php

namespace App\Repositories;

use App\Helpers\ReferenceGenerator;
use App\Http\Requests\ActionDomainRequest;
use App\Http\Resources\ActionDomainResource;
use App\Models\ActionDomain;
use App\Models\ActionDomainState;
use App\Models\ActionDomainStatus;
use App\Models\Beneficiary;
use App\Models\FundingSource;
use App\Models\User;
use App\Support\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ActionDomainRepository
{
    /**
     * List action domains with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['action_domains.name', 'action_domains.reference', 'responsible'];
        $sortable = ['name', 'reference', 'status', 'state', 'responsible', 'budget', 'start_date', 'end_date'];


        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = ActionDomain::select(
            'action_domains.id',
            'action_domains.uuid',
            'action_domains.reference',
            'action_domains.name',
            'action_domains.start_date',
            'action_domains.end_date',
            'action_domains.budget',
            'action_domains.status',
            'action_domains.state',
            'action_domains.responsible_uuid',
            'action_domains.currency',
            'responsibles.name as responsible',
        )
            ->leftJoin('users as responsibles', 'action_domains.responsible_uuid', '=', 'responsibles.uuid');

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'responsible') {
                        $q->orWhere('responsibles.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'responsible') {
            $query->orderBy('responsibles.name', $sortOrder);
        } else {
            $query->orderBy("action_domains.$sortBy", $sortOrder);
        }

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Load requirements data
     */
    public function requirements()
    {
        $currency = Currency::getDefault();

        $responsibles = User::whereHas('employee')->select('uuid', 'name')
            ->where('status', true)->orderBy('id', 'desc')
            ->get();

        $beneficiaries = Beneficiary::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name')
            ->get();

        $fundingSources = FundingSource::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name')
            ->get();

        return [
            'currency' => $currency,
            'responsibles' => $responsibles,
            'beneficiaries' => $beneficiaries,
            'funding_sources' => $fundingSources,
        ];
    }

    /**
     * Create a new action domain.
     */
    public function store(ActionDomainRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'responsible_uuid' => $request->input('responsible'),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $actionDomain = ActionDomain::create($request->only([
                'name',
                'start_date',
                'end_date',
                'currency',
                'responsible_uuid',
                'description',
                'prerequisites',
                'impacts',
                'risks',
                'created_by',
                'updated_by'
            ]));

            $beneficiaryUuids = collect($request->beneficiaries)
                ->pluck('uuid')
                ->filter()
                ->toArray();

            $validBeneficiaries = Beneficiary::whereIn('uuid', $beneficiaryUuids)
                ->pluck('uuid')
                ->toArray();
            $actionDomain->beneficiaries()->sync($validBeneficiaries);

            $requestedUuids = collect($request->funding_sources)
                ->pluck('uuid')
                ->filter()
                ->toArray();

            $validFundingSources = FundingSource::whereIn('uuid', $requestedUuids)
                ->pluck('uuid')
                ->toArray();

            $totalBudget = 0;
            foreach ($request->funding_sources as $source) {
                if (in_array($source['uuid'], $validFundingSources)) {
                    $plannedBudget = $source['planned_amount'] ?? 0;
                    $actionDomain->fundingSources()->attach($source['uuid'], [
                        'planned_budget' => $plannedBudget,
                    ]);
                    $totalBudget += $plannedBudget;
                }
            }

            $actionDomain->refresh();

            //Save initial status
            $status = ActionDomainStatus::create([
                'action_domain_uuid' => $actionDomain->uuid,
                'action_domain_id' => $actionDomain->id,
                'status_code' => $actionDomain->status,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            //Save initial state
            $state = ActionDomainState::create([
                'action_domain_uuid' => $actionDomain->uuid,
                'action_domain_id' => $actionDomain->id,
                'state_code' => $actionDomain->state,
                'state_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $actionDomain->update([
                'reference' => ReferenceGenerator::generateProgramReference($actionDomain->id),
                'budget' => $totalBudget,
                'status' => $status->status_code,
                'status_changed_at' => $status->status_date,
                'status_changed_by' => $status->created_by,
                'state' => $state->state_code,
                'state_changed_at' => $state->state_date,
                'state_changed_by' => $state->created_by,
            ]);

            DB::commit();

            $actionDomain->loadMissing(['responsible', 'beneficiaries', 'fundingSources']);

            return (new ActionDomainResource($actionDomain))->additional([
                'mode' => $request->input('mode', 'view')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show a specific action domain.
     */
    public function show(ActionDomain $actionDomain)
    {
        return ['action_domain' => new ActionDomainResource($actionDomain->loadMissing(['responsible', 'beneficiaries', 'fundingSources']))];
    }

    /**
     * Update an action domain.
     */
    public function update(ActionDomainRequest $request, ActionDomain $actionDomain)
    {
        DB::beginTransaction();
        try {

            $request->merge([
                'responsible_uuid' => $request->input('responsible'),
                'updated_by' => Auth::user()?->uuid,
            ]);

            $actionDomain->fill($request->only([
                'name',
                'description',
                'start_date',
                'end_date',
                'budget',
                'currency',
                'responsible_uuid',
                'description',
                'prerequisites',
                'impacts',
                'risks',
                'updated_by'
            ]))->save();

            $beneficiaryUuids = collect($request->beneficiaries)
                ->pluck('uuid')
                ->filter()
                ->toArray();
            $validBeneficiaries = Beneficiary::whereIn('uuid', $beneficiaryUuids)
                ->pluck('uuid')
                ->toArray();
            $actionDomain->beneficiaries()->sync($validBeneficiaries);

            $actionDomain->fundingSources()->detach();

            $requestedUuids = collect($request->funding_sources)
                ->pluck('uuid')
                ->filter()
                ->toArray();

            $validFundingSources = FundingSource::whereIn('uuid', $requestedUuids)
                ->pluck('uuid')
                ->toArray();

            $totalBudget = 0;
            foreach ($request->funding_sources as $source) {
                if (in_array($source['uuid'], $validFundingSources)) {
                    $plannedBudget = $source['planned_amount'] ?? 0;
                    $actionDomain->fundingSources()->attach($source['uuid'], [
                        'planned_budget' => $plannedBudget,
                    ]);
                    $totalBudget += $plannedBudget;
                }
            }

            DB::commit();

            $actionDomain->update(['budget' => $totalBudget]);
            $actionDomain->load(['responsible', 'beneficiaries', 'fundingSources']);

            return (new ActionDomainResource($actionDomain))->additional([
                'mode' => $request->input('mode', 'edit')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete multiple action domains.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = ActionDomain::whereIn('id', $ids)->delete();
            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
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
}
