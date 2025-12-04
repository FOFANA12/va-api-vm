<?php

namespace App\Repositories;

use App\Helpers\ReferenceGenerator;
use App\Http\Requests\CapabilityDomainRequest;
use App\Http\Resources\CapabilityDomainResource;
use App\Models\Beneficiary;
use App\Models\CapabilityDomain;
use App\Models\CapabilityDomainState;
use App\Models\CapabilityDomainStatus;
use App\Support\Currency;
use App\Models\FundingSource;
use App\Models\StrategicDomain;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CapabilityDomainRepository
{
    /**
     * List capability domains with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['capability_domains.name', 'strategic_domain', 'capability_domains.reference', 'responsible'];
        $sortable = ['name', 'strategic_domain', 'reference', 'status', 'state', 'responsible', 'budget', 'start_date', 'end_date'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = CapabilityDomain::select(
            'capability_domains.id',
            'capability_domains.uuid',
            'capability_domains.reference',
            'capability_domains.name',
            'capability_domains.start_date',
            'capability_domains.end_date',
            'capability_domains.budget',
            'capability_domains.status',
            'capability_domains.state',
            'capability_domains.responsible_uuid',
            'capability_domains.currency',
            'responsibles.name as responsible',
            'strategic_domains.name as strategicDomain'
        )
            ->leftJoin('users as responsibles', 'capability_domains.responsible_uuid', '=', 'responsibles.uuid')
            ->leftJoin('strategic_domains', 'capability_domains.strategic_domain_uuid', '=', 'strategic_domains.uuid');

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'responsible') {
                        $q->orWhere('responsibles.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else  if ($column === 'strategic_domain') {
                        $q->orWhere('strategic_domains.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'responsible') {
            $query->orderBy('responsibles.name', $sortOrder);
        } else if ($sortBy === 'strategic_domain') {
            $query->orderBy('strategic_domains.name', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Load requirements data for forms.
     */
    public function requirements()
    {
        $currency = Currency::getDefault();

        $responsibles = User::whereHas('employee')->select('uuid', 'name')
            ->where('status', true)->orderBy('id', 'desc')
            ->get();

        $strategicDomains = StrategicDomain::select('uuid', 'name')
            ->where('status', '!=', 'done')
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
            'strategic_domains' => $strategicDomains,
            'beneficiaries' => $beneficiaries,
            'funding_sources' => $fundingSources,
        ];
    }
    /**
     * Store a new capability domain.
     */
    public function store(CapabilityDomainRequest $request)
    {

        DB::beginTransaction();
        try {
            $request->merge([
                'strategic_domain_uuid' => $request->input('strategic_domain'),
                'responsible_uuid' => $request->input('responsible'),
                'created_uuid' => Auth::user()?->uuid,
                'updated_uuid' => Auth::user()?->uuid,
            ]);

            $capabilityDomain = CapabilityDomain::create($request->only([
                'name',
                'strategic_domain_uuid',
                'start_date',
                'end_date',
                'currency',
                'responsible_uuid',
                'description',
                'prerequisites',
                'impacts',
                'risks',
                'created_by',
                'updated_by',
            ]));

            $beneficiaryUuids = collect($request->beneficiaries)
                ->pluck('uuid')
                ->filter()
                ->toArray();

            $validBeneficiaries = Beneficiary::whereIn('uuid', $beneficiaryUuids)
                ->pluck('uuid')
                ->toArray();
            $capabilityDomain->beneficiaries()->sync($validBeneficiaries);

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
                    $capabilityDomain->fundingSources()->attach($source['uuid'], [
                        'planned_budget' => $plannedBudget,
                    ]);
                    $totalBudget += $plannedBudget;
                }
            }

            $capabilityDomain->refresh();

            //Save initial status
            $status = CapabilityDomainStatus::create([
                'capability_domain_uuid' => $capabilityDomain->uuid,
                'capability_domain_id' => $capabilityDomain->id,
                'status_code' => $capabilityDomain->status,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            //Save initial state
            $state = CapabilityDomainState::create([
                'capability_domain_uuid' => $capabilityDomain->uuid,
                'capability_domain_id' => $capabilityDomain->id,
                'state_code' => $capabilityDomain->state,
                'state_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $capabilityDomain->update([
                'reference' => ReferenceGenerator::generateActivityReference($capabilityDomain->id),
                'budget' => $totalBudget,
                'status' => $status->status_code,
                'status_changed_at' => $status->status_date,
                'status_changed_by' => $status->created_by,
                'state' => $state->state_code,
                'state_changed_at' => $state->state_date,
                'state_changed_by' => $state->created_by,
            ]);

            DB::commit();

            $capabilityDomain->loadMissing(['responsible', 'beneficiaries', 'fundingSources']);

            return (new CapabilityDomainResource($capabilityDomain))->additional([
                'mode' => $request->input('mode', 'view')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show a specific capability domain.
     */
    public function show(CapabilityDomain $capabilityDomain)
    {
        return ['capability_domain' => new CapabilityDomainResource($capabilityDomain->loadMissing(['responsible', 'beneficiaries', 'fundingSources']))];
    }

    /**
     * Update an existing capability domain.
     */
    public function update(CapabilityDomainRequest $request, CapabilityDomain $capabilityDomain)
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'strategic_domain_uuid' => $request->input('strategic_domain'),
                'responsible_uuid' => $request->input('responsible'),
                'updated_by' => Auth::user()?->uuid,
            ]);

            $capabilityDomain->fill($request->only([
                'name',
                'start_date',
                'end_date',
                'currency',
                'strategic_domain_uuid',
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
            $capabilityDomain->beneficiaries()->sync($validBeneficiaries);

            $capabilityDomain->fundingSources()->detach();

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
                    $capabilityDomain->fundingSources()->attach($source['uuid'], [
                        'planned_budget' => $plannedBudget,
                    ]);
                    $totalBudget += $plannedBudget;
                }
            }

            DB::commit();

            $capabilityDomain->update(['budget' => $totalBudget]);
            $capabilityDomain->load(['responsible', 'beneficiaries', 'fundingSources']);

            return (new CapabilityDomainResource($capabilityDomain))->additional([
                'mode' => $request->input('mode', 'edit')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete one or multiple capability domain(s).
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = CapabilityDomain::whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            DB::commit();
        } catch (RuntimeException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e->getCode() === '23000') {
                throw new \Exception(__('app/common.repository.foreignKey'));
            }

            throw new \Exception(__('app/common.repository.error'));
        }
    }
}
