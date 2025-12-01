<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use RuntimeException;
use App\Models\Decision;
use Illuminate\Http\Request;
use App\Support\PriorityLevel;
use Illuminate\Support\Facades\DB;
use App\Helpers\ReferenceGenerator;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DecisionRequest;
use App\Http\Resources\DecisionResource;
use App\Models\Action;
use App\Models\DecisionStatus;
use App\Models\StrategicObjective;

class DecisionRepository
{
    /**
     * List decisions with pagination, filters, and sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['title', 'priority', 'description', 'user', 'reference'];
        $sortable   = ['decision_date', 'title', 'reference', 'priority', 'status', 'user'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = Decision::query()
            ->join('users', 'decisions.created_by', '=', 'users.uuid')
            ->leftJoin('attachments', function ($join) {
                $join->on('attachments.attachable_id', '=', 'decisions.id')
                    ->where('attachments.attachable_type', '=', Decision::tableName());
            })
            ->select(
                'decisions.id',
                'decisions.uuid',
                'decisions.title',
                'decisions.reference',
                'decisions.decision_date',
                'decisions.description',
                'decisions.decidable_type',
                'decisions.decidable_id',
                'decisions.priority',
                'decisions.status',
                'users.name as user',
                'attachments.id as attachment_id',
            );

        if ($request->filled('decidableId') && $request->filled('decidableType')) {
            $query->where('decisions.decidable_id', $request->decidableId)
                ->where('decisions.decidable_type', $request->decidableType);
        }

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                $term = '%' . $searchTerm . '%';
                foreach ($searchable as $column) {
                    if ($column === 'user') {
                        $q->orWhereRaw("LOWER(users.name) LIKE LOWER(?)", [$term]);
                    } else {
                        $q->orWhereRaw("LOWER(decisions.$column) LIKE LOWER(?)", [$term]);
                    }
                }
            });
        }

        if ($sortBy === 'user') {
            $query->orderBy('users.name', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
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
        $priorityLevels =  collect(PriorityLevel::all())->map(function ($item) {
            return [
                'code' => $item['code'],
                'color' => $item['color'],
                'name' => $item['name'][app()->getLocale()] ?? $item['name']['fr'],
            ];
        });

        $decidableType = $request->get('decidableType');
        $decidableId   = $request->get('decidableId');
        $canHaveDecision = false;


        if ($decidableType && $decidableId) {
            switch ($decidableType) {
                case 'actions':
                    $action = Action::find($decidableId);
                    if ($action) {
                        $canHaveDecision = $action->status === 'in_progress';
                    }
                    break;

                case 'strategic_objectives':
                    $objective = StrategicObjective::find($decidableId);
                    if ($objective) {
                        $canHaveDecision = $objective->status === 'engaged';
                    }
                    break;
            }
        }

        return [
            'priority_levels' => $priorityLevels,
            'can_have_decision' => $canHaveDecision,
        ];
    }

    /**
     * Store a new decision.
     */
    public function store(Request $request)
    {
        $identifier = null;

        DB::beginTransaction();
        try {
            $request->merge([
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $decision = Decision::create($request->only([
                'title',
                'decision_date',
                'description',
                'priority',
                'decidable_type',
                'decidable_id',
                'created_by',
                'updated_by',
            ]));

            $decidableReference = $decision->decidable->reference;

            $decision->update([
                'reference' => ReferenceGenerator::generateDecisionReference(
                    $decision->id,
                    $decidableReference,
                ),
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $identifier = FileHelper::upload($file, 'uploads');

                $decision->attachment()->create([
                    'title' => $request->input('title', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'identifier' => $identifier,
                    'size' => $file->getSize(),
                    'uploaded_by' => Auth::user()->uuid,
                    'uploaded_at' => now(),
                ]);
            }

            $decision->refresh();

            //Save initial status
            DecisionStatus::create([
                'decision_uuid' => $decision->uuid,
                'status' => $decision->status,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            DB::commit();

            $decision->loadMissing(['decidable', 'attachment']);

            return (new DecisionResource($decision))->additional([
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
     * Show a specific decision.
     */
    public function show(Decision $decision)
    {
        return ['decision' => new DecisionResource($decision->load(['decidable', 'attachment']))];
    }

    /**
     * Update a decision.
     */
    public function update(DecisionRequest $request, Decision $decision)
    {
        $oldFile = $decision->attachment?->identifier;
        $newFile = null;

        DB::beginTransaction();
        try {

            $request->merge([
                'updated_by' => Auth::user()?->uuid,
            ]);

            $decision->fill($request->only([
                'title',
                'decision_date',
                'description',
                'priority',
                'updated_by',
            ]));

            $decision->save();

            if ($request->boolean('delete_file') && $oldFile) {
                FileHelper::delete("uploads/{$oldFile}");
                $decision->attachment()->delete();
            }

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $newFile = FileHelper::upload($file, 'uploads');

                if ($oldFile && $oldFile !== $newFile) {
                    FileHelper::delete("uploads/{$oldFile}");
                    $decision->attachment()->delete();
                }

                $decision->attachment()->create([
                    'title' => $request->input('title', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'identifier' => $newFile,
                    'size' => $file->getSize(),
                    'uploaded_by' => Auth::user()->uuid,
                    'uploaded_at' => now(),
                ]);
            }

            DB::commit();

            $decision->load(['decidable', 'attachment']);

            return (new DecisionResource($decision))->additional([
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
     * Delete decision(s).
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
            $decisions = Decision::with('attachment')->whereIn('id', $ids)->get();

            if ($decisions->isEmpty()) {
                throw new RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            foreach ($decisions as $decision) {
                if ($decision->attachment) {
                    $filesToDelete[] = "uploads/{$decision->attachment->identifier}";
                    $decision->attachment->delete();
                }
                $decision->delete();
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
}
