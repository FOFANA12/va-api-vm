<?php

namespace App\Repositories;

use App\Helpers\DateTimeFormatter;
use App\Helpers\FileHelper;
use RuntimeException;
use Illuminate\Http\Request;
use App\Models\DecisionStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DecisionStatusRequest;
use App\Http\Resources\DecisionStatusResource;
use App\Models\Decision;
use App\Support\DecisionStatus as SupportDecisionStatus;

class DecisionStatusRepository
{
    /**
     * List decision statuses with pagination, filters, and sorting.
     */
    public function index(Request $request)
    {
        $sortable = ['status_date', 'status', 'user'];

        $decisionId = $request->input('decisionId');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = DecisionStatus::query()
            ->join('decisions', 'decision_statuses.decision_uuid', '=', 'decisions.uuid')
            ->join('users', 'decision_statuses.created_by', '=', 'users.uuid')
            ->leftJoin('attachments', function ($join) {
                $join->on('attachments.attachable_id', '=', 'decision_statuses.id')
                    ->where('attachments.attachable_type', '=', DecisionStatus::tableName());
            })
            ->select(
                'decision_statuses.id',
                'decision_statuses.uuid',
                'decision_statuses.status_date',
                'decision_statuses.comment',
                'decision_statuses.status',
                'users.name as user',
                'attachments.id as attachment_id',
            );

        if (!empty($decisionId)) {
            $query->where('decisions.id', $decisionId);
        }

        if ($sortBy === 'user') {
            $query->orderBy('users.name', $sortOrder);
        } else {
            $query->orderBy("decision_statuses.$sortBy", $sortOrder);
        }

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Load requirements data
     */
    public function requirements(Decision $decision)
    {
        $current = $decision->status;
        $next = SupportDecisionStatus::next($current);

        $decisionStatuses =  collect($next)->map(function ($code) {
            $status = SupportDecisionStatus::get($code, app()->getLocale());
            return [
                'code'  => $status->code,
                'name'  => $status->label,
                'color' => $status->color,
            ];
        })->values();

        return [
            'statuses' => $decisionStatuses,
        ];
    }

    /**
     * Store a new decision status and update the current status of the decision.
     */
    public function store(DecisionStatusRequest $request, Decision $decision)
    {
        $identifier = null;
        DB::beginTransaction();
        try {
            $request->merge([
                'status_date' => now(),
                "created_by" => Auth::user()?->uuid,
                "updated_by" => Auth::user()?->uuid,
            ]);

            $decisionStatus = $decision->statuses()->create($request->only([
                "status_date",
                "status",
                "comment",
                "created_by",
                "updated_by",
            ]));

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $identifier = FileHelper::upload($file, 'uploads');

                $decisionStatus->attachment()->create([
                    'title' => $request->input('title', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'identifier' => $identifier,
                    'size' => $file->getSize(),
                    'uploaded_by' => Auth::user()->uuid,
                    'uploaded_at' => now(),
                ]);
            }

            $decision->status = $decisionStatus->status;
            $decision->status_changed_by = $decisionStatus->created_by;
            $decision->status_changed_at = $decisionStatus->created_at;
            $decision->save();

            $decisionStatus->loadMissing(['decision', 'attachment']);
            $decisionStatus->refresh();

            DB::commit();

            return [
                'decision' => [
                    'status' => SupportDecisionStatus::get($decision->status, app()->getLocale()),
                    'status_changed_at' => $decision->status_changed_at ? DateTimeFormatter::formatDatetime($decision->status_changed_at) : null,
                    'status_changed_by' => $decision->statusChangedBy?->name,
                ],
                'new_status' => new DecisionStatusResource($decisionStatus),
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show a specific decision status.
     */
    public function show(DecisionStatus $decisionStatus)
    {
        $decisionStatus->load(['decision', 'attachment']);
        return ['decision_status' => new DecisionStatusResource($decisionStatus)];
    }

    /**
     * Delete multiple decision status.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');
        $decisionId = $request->input('decision_id');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        $filesToDelete = [];

        try {
            DB::transaction(function () use ($ids, $decisionId, &$filesToDelete) {
                $statuses = DecisionStatus::with('attachment')->whereIn('id', $ids)->get();

                if ($statuses->isEmpty()) {
                    throw new RuntimeException(__('app/common.destroy.no_items_deleted'));
                }

                foreach ($statuses as $status) {
                    if ($status->attachment) {
                        $filesToDelete[] = "uploads/{$status->attachment->identifier}";
                        $status->attachment->delete();
                    }
                    $status->delete();
                }

                if ($decisionId) {
                    $decision = Decision::find($decisionId);

                    if ($decision) {
                        $lastStatus = $decision->statuses()->orderBy('id', 'desc')->first();

                        if ($lastStatus) {
                            $decision->status = $lastStatus->status;
                            $decision->status_changed_by = $lastStatus->created_by;
                            $decision->status_changed_at = $lastStatus->created_at;
                        } else {
                            $decision->status = 'none';
                            $decision->status_changed_by = null;
                            $decision->status_changed_at = null;
                        }

                        $decision->save();
                    }
                }
            });

            foreach ($filesToDelete as $filePath) {
                FileHelper::delete($filePath);
            }
        } catch (RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            if ($e->getCode() === "23000") {
                throw new \Exception(__('app/common.repository.foreignKey'));
            }
            throw new \Exception(__('app/common.repository.error'));
        }
    }
}
