<?php

namespace App\Repositories;

use App\Helpers\ReferenceGenerator;
use App\Http\Requests\ProgramRequest;
use App\Http\Resources\ProgramResource;
use App\Models\Beneficiary;
use App\Models\FundingSource;
use App\Models\Program;
use App\Models\ProgramState;
use App\Models\ProgramStatus;
use App\Models\User;
use App\Support\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProgramRepository
{
    /**
     * List programs with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['programs.name', 'programs.reference', 'responsible'];
        $sortable = ['name', 'reference', 'status', 'state', 'responsible', 'budget', 'start_date', 'end_date'];


        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = Program::select(
            'programs.id',
            'programs.uuid',
            'programs.reference',
            'programs.name',
            'programs.start_date',
            'programs.end_date',
            'programs.budget',
            'programs.status',
            'programs.state',
            'programs.responsible_uuid',
            'programs.currency',
            'responsibles.name as responsible',
        )
            ->leftJoin('users as responsibles', 'programs.responsible_uuid', '=', 'responsibles.uuid');

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
            $query->orderBy("programs.$sortBy", $sortOrder);
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
     * Create a new program.
     */
    public function store(ProgramRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'responsible_uuid' => $request->input('responsible'),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $program = Program::create($request->only([
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
            $program->beneficiaries()->sync($validBeneficiaries);

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
                    $program->fundingSources()->attach($source['uuid'], [
                        'planned_budget' => $plannedBudget,
                    ]);
                    $totalBudget += $plannedBudget;
                }
            }

            $program->refresh();

            //Save initial status
            $status = ProgramStatus::create([
                'program_uuid' => $program->uuid,
                'program_id' => $program->id,
                'status_code' => $program->status,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            //Save initial state
            $state = ProgramState::create([
                'program_uuid' => $program->uuid,
                'program_id' => $program->id,
                'state_code' => $program->state,
                'state_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $program->update([
                'reference' => ReferenceGenerator::generateProgramReference($program->id),
                'budget' => $totalBudget,
                'status' => $status->status_code,
                'status_changed_at' => $status->status_date,
                'status_changed_by' => $status->created_by,
                'state' => $state->state_code,
                'state_changed_at' => $state->state_date,
                'state_changed_by' => $state->created_by,
            ]);

            DB::commit();

            $program->loadMissing(['responsible', 'beneficiaries', 'fundingSources']);

            return (new ProgramResource($program))->additional([
                'mode' => $request->input('mode', 'view')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show a specific program.
     */
    public function show(Program $program)
    {
        return ['program' => new ProgramResource($program->loadMissing(['responsible', 'beneficiaries', 'fundingSources']))];
    }

    /**
     * Update a program.
     */
    public function update(ProgramRequest $request, Program $program)
    {
        DB::beginTransaction();
        try {

            $request->merge([
                'responsible_uuid' => $request->input('responsible'),
                'updated_by' => Auth::user()?->uuid,
            ]);

            $program->fill($request->only([
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
            $program->beneficiaries()->sync($validBeneficiaries);

            $program->fundingSources()->detach();

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
                    $program->fundingSources()->attach($source['uuid'], [
                        'planned_budget' => $plannedBudget,
                    ]);
                    $totalBudget += $plannedBudget;
                }
            }

            DB::commit();

            $program->update(['budget' => $totalBudget]);
            $program->load(['responsible', 'beneficiaries', 'fundingSources']);

            return (new ProgramResource($program))->additional([
                'mode' => $request->input('mode', 'edit')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete multiple programs.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = Program::whereIn('id', $ids)->delete();
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
