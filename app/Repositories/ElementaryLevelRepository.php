<?php

namespace App\Repositories;

use App\Helpers\ReferenceGenerator;
use App\Http\Requests\ElementaryLevelRequest;
use App\Http\Resources\ElementaryLevelResource;
use App\Models\Beneficiary;
use App\Models\CapabilityDomain;
use App\Models\ElementaryLevel;
use App\Models\ElementaryLevelState;
use App\Models\ElementaryLevelStatus;
use App\Support\Currency;
use App\Models\FundingSource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ElementaryLevelRepository
{
    /**
     * List elementary levels with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['elementary_levels.name', 'capability_domain', 'elementary_levels.reference', 'responsible'];
        $sortable = ['name', 'capability_domain', 'reference', 'status', 'state', 'responsible', 'budget', 'start_date', 'end_date'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = ElementaryLevel::select(
            'elementary_levels.id',
            'elementary_levels.uuid',
            'elementary_levels.reference',
            'elementary_levels.name',
            'elementary_levels.start_date',
            'elementary_levels.end_date',
            'elementary_levels.budget',
            'elementary_levels.status',
            'elementary_levels.state',
            'elementary_levels.responsible_uuid',
            'elementary_levels.currency',
            'responsibles.name as responsible',
            'capability_domains.name as capabilityDomain'
        )
            ->leftJoin('users as responsibles', 'elementary_levels.responsible_uuid', '=', 'responsibles.uuid')
            ->leftJoin('capability_domains', 'elementary_levels.capability_domain_uuid', '=', 'capability_domains.uuid');

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'responsible') {
                        $q->orWhere('responsibles.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else  if ($column === 'capability_domain') {
                        $q->orWhere('capability_domains.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'responsible') {
            $query->orderBy('responsibles.name', $sortOrder);
        } else if ($sortBy === 'capability_domain') {
            $query->orderBy('capability_domains.name', $sortOrder);
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

        $capabilityDomains = CapabilityDomain::select('uuid', 'name')
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
            'capability_domains' => $capabilityDomains,
            'beneficiaries' => $beneficiaries,
            'funding_sources' => $fundingSources,
        ];
    }
    /**
     * Store a new elementary level.
     */
    public function store(ElementaryLevelRequest $request)
    {

        DB::beginTransaction();
        try {
            $request->merge([
                'capability_domain_uuid' => $request->input('capability_domain'),
                'responsible_uuid' => $request->input('responsible'),
                'created_uuid' => Auth::user()?->uuid,
                'updated_uuid' => Auth::user()?->uuid,
            ]);

            $elementaryLevel = ElementaryLevel::create($request->only([
                'name',
                'capability_domain_uuid',
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
            $elementaryLevel->beneficiaries()->sync($validBeneficiaries);

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
                    $elementaryLevel->fundingSources()->attach($source['uuid'], [
                        'planned_budget' => $plannedBudget,
                    ]);
                    $totalBudget += $plannedBudget;
                }
            }

            $elementaryLevel->refresh();

            //Save initial status
            $status = ElementaryLevelStatus::create([
                'elementary_level_uuid' => $elementaryLevel->uuid,
                'elementary_level_id' => $elementaryLevel->id,
                'status_code' => $elementaryLevel->status,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            //Save initial state
            $state = ElementaryLevelState::create([
                'elementary_level_uuid' => $elementaryLevel->uuid,
                'elementary_level_id' => $elementaryLevel->id,
                'state_code' => $elementaryLevel->state,
                'state_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $elementaryLevel->update([
                'reference' => ReferenceGenerator::generateElementaryLevelReference($elementaryLevel->id),
                'budget' => $totalBudget,
                'status' => $status->status_code,
                'status_changed_at' => $status->status_date,
                'status_changed_by' => $status->created_by,
                'state' => $state->state_code,
                'state_changed_at' => $state->state_date,
                'state_changed_by' => $state->created_by,
            ]);

            DB::commit();

            $elementaryLevel->loadMissing(['responsible', 'beneficiaries', 'fundingSources']);

            return (new ElementaryLevelResource($elementaryLevel))->additional([
                'mode' => $request->input('mode', 'view')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show a specific elementary level.
     */
    public function show(ElementaryLevel $elementaryLevel)
    {
        return ['elementary_level' => new ElementaryLevelResource($elementaryLevel->loadMissing(['responsible', 'beneficiaries', 'fundingSources']))];
    }

    /**
     * Update an existing elementary level.
     */
    public function update(ElementaryLevelRequest $request, ElementaryLevel $elementaryLevel)
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'capability_domain_uuid' => $request->input('capability_domain'),
                'responsible_uuid' => $request->input('responsible'),
                'updated_by' => Auth::user()?->uuid,
            ]);

            $elementaryLevel->fill($request->only([
                'name',
                'start_date',
                'end_date',
                'currency',
                'capability_domain_uuid',
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
            $elementaryLevel->beneficiaries()->sync($validBeneficiaries);

            $elementaryLevel->fundingSources()->detach();

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
                    $elementaryLevel->fundingSources()->attach($source['uuid'], [
                        'planned_budget' => $plannedBudget,
                    ]);
                    $totalBudget += $plannedBudget;
                }
            }

            DB::commit();

            $elementaryLevel->update(['budget' => $totalBudget]);
            $elementaryLevel->load(['responsible', 'beneficiaries', 'fundingSources']);

            return (new ElementaryLevelResource($elementaryLevel))->additional([
                'mode' => $request->input('mode', 'edit')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete one or multiple elementary level(s).
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = ElementaryLevel::whereIn('id', $ids)->delete();

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
