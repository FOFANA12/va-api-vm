<?php

namespace App\Repositories;

use App\Helpers\ReferenceGenerator;
use App\Http\Requests\StrategicDomainRequest;
use App\Http\Resources\StrategicDomainResource;
use App\Models\ActionDomain;
use App\Models\Beneficiary;
use App\Support\Currency;
use App\Models\FundingSource;
use App\Models\StrategicDomain;
use App\Models\StrategicDomainState;
use App\Models\StrategicDomainStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StrategicDomainRepository
{
    /**
     * List porjects with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['strategic_domains.name', 'action_domain', 'strategic_domains.reference', 'responsible'];
        $sortable = ['name', 'action_domain', 'reference', 'status', 'state', 'responsible', 'budget', 'start_date', 'end_date'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = StrategicDomain::select(
            'strategic_domains.id',
            'strategic_domains.uuid',
            'strategic_domains.reference',
            'strategic_domains.name',
            'strategic_domains.start_date',
            'strategic_domains.end_date',
            'strategic_domains.budget',
            'strategic_domains.status',
            'strategic_domains.state',
            'strategic_domains.responsible_uuid',
            'strategic_domains.currency',
            'responsibles.name as responsible',
            'action_domains.name as actionDomain'
        )
            ->leftJoin('users as responsibles', 'strategic_domains.responsible_uuid', '=', 'responsibles.uuid')
            ->join('action_domains', 'strategic_domains.action_domain_uuid', '=', 'action_domains.uuid');

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'responsible') {
                        $q->orWhere('responsibles.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else  if ($column === 'action_domain') {
                        $q->orWhere('action_domains.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'responsible') {
            $query->orderBy('responsibles.name', $sortOrder);
        } else if ($sortBy === 'action_domain') {
            $query->orderBy('action_domains.name', $sortOrder);
        } else {
            $query->orderBy("strategic_domains.$sortBy", $sortOrder);
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

        $actionDomains = ActionDomain::select('uuid', 'name')
            ->whereNotIn('status', ['closed', 'stopped'])
            ->orderBy('id', 'desc')
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
            'action_domains' => $actionDomains,
            'beneficiaries' => $beneficiaries,
            'funding_sources' => $fundingSources,
        ];
    }

    /**
     * Create a new strategic domain.
     */
    public function store(StrategicDomainRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'action_domain_uuid' => $request->input('action_domain'),
                'responsible_uuid' => $request->input('responsible'),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $strategicDomain = StrategicDomain::create($request->only([
                'name',
                'action_domain_uuid',
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
            $strategicDomain->beneficiaries()->sync($validBeneficiaries);

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
                    $strategicDomain->fundingSources()->attach($source['uuid'], [
                        'planned_budget' => $plannedBudget,
                    ]);
                    $totalBudget += $plannedBudget;
                }
            }

            $strategicDomain->refresh();

            //Save initial status
            $status = StrategicDomainStatus::create([
                'strategic_domain_uuid' => $strategicDomain->uuid,
                'strategic_domain_id' => $strategicDomain->id,
                'status_code' => $strategicDomain->status,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            //Save initial state
            $state = StrategicDomainState::create([
                'strategic_domain_uuid' => $strategicDomain->uuid,
                'strategic_domain_id' => $strategicDomain->id,
                'state_code' => $strategicDomain->state,
                'state_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $strategicDomain->update([
                'reference' => ReferenceGenerator::generateProjectReference($strategicDomain->id),
                'budget' => $totalBudget,
                'status' => $status->status_code,
                'status_changed_at' => $status->status_date,
                'status_changed_by' => $status->created_by,
                'state' => $state->state_code,
                'state_changed_at' => $state->state_date,
                'state_changed_by' => $state->created_by,
            ]);

            DB::commit();

            $strategicDomain->loadMissing(['responsible', 'beneficiaries', 'fundingSources']);

            return (new StrategicDomainResource($strategicDomain))->additional([
                'mode' => $request->input('mode', 'view')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show a specific strategic domain.
     */
    public function show(StrategicDomain $strategicDomain)
    {
        return ['strategic_domain' => new StrategicDomainResource($strategicDomain->loadMissing(['responsible', 'beneficiaries', 'fundingSources']))];
    }

    /**
     * Update a strategic domain.
     */
    public function update(StrategicDomainRequest $request, StrategicDomain $strategicDomain)
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'action_domain_uuid' => $request->input('action_domain'),
                'responsible_uuid' => $request->input('responsible'),
                'updated_by' => Auth::user()?->uuid,
            ]);

            $strategicDomain->fill($request->only([
                'name',
                'start_date',
                'end_date',
                'currency',
                'action_domain_uuid',
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
            $strategicDomain->beneficiaries()->sync($validBeneficiaries);

            $strategicDomain->fundingSources()->detach();

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
                    $strategicDomain->fundingSources()->attach($source['uuid'], [
                        'planned_budget' => $plannedBudget,
                    ]);
                    $totalBudget += $plannedBudget;
                }
            }

            DB::commit();

            $strategicDomain->update(['budget' => $totalBudget]);
            $strategicDomain->load(['responsible', 'beneficiaries', 'fundingSources']);

            return (new StrategicDomainResource($strategicDomain))->additional([
                'mode' => $request->input('mode', 'edit')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete multiple strategic domain.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = StrategicDomain::whereIn('id', $ids)->delete();
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
