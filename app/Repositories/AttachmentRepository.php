<?php

namespace App\Repositories;

use App\Http\Requests\AttachmentRequest;
use App\Models\Attachment;
use App\Helpers\FileHelper;
use App\Http\Resources\AttachmentResource;
use App\Models\FileType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttachmentRepository
{
    public function index(Request $request)
    {
        $searchable = ['title', 'original_name', 'mime_type', 'uploaded_by', 'file_type'];
        $sortable   = ['title', 'original_name', 'mime_type', 'file_type', 'uploaded_at', 'uploaded_by'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = Attachment::leftJoin('users', 'attachments.uploaded_by', '=', 'users.uuid')
            ->leftJoin('file_types', 'attachments.file_type_uuid', '=', 'file_types.uuid')
            ->select(
                'attachments.id',
                'attachments.uuid',
                'attachments.title',
                'attachments.original_name',
                'attachments.mime_type',
                'attachments.identifier',
                'attachments.size',
                'attachments.uploaded_at',
                'attachments.attachable_id',
                'attachments.attachable_type',
                'attachments.uploaded_by',
                'users.name as uploader_name',
                'file_types.name as file_type',
            );

        if ($request->filled('attachableId') && $request->filled('attachableType')) {
            $query->where('attachments.attachable_id', $request->attachableId)
                ->where('attachments.attachable_type', $request->attachableType);
        }

        if ($request->filled('filterFileType')) {
            $query->where('file_types.id', $request->input('filterFileType'));
        }

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'uploaded_by') {
                        $q->orWhere('users.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else if ($column === 'file_type') {
                        $q->orWhere('file_types.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere("attachments.$column", 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'uploaded_by') {
            $query->orderBy('users.name', $sortOrder);
        } else if ($sortBy === 'file_type') {
            $query->orderBy('file_types.name', $sortOrder);
        } else {
            $query->orderBy("attachments.$sortBy", $sortOrder);
        }

        $query->orderBy($sortBy, $sortOrder);

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Load requirements data
     */
    public function requirements()
    {
        $fileTypes = FileType::where('status', true)
            ->orderBy('id', 'desc')
            ->select('id', 'uuid', 'name')
            ->get();

        return [
            'file_types' => $fileTypes,
        ];
    }

    public function store(AttachmentRequest $request)
    {
        DB::beginTransaction();
        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                $identifier = FileHelper::upload($file, 'uploads');

                $attachment = Attachment::create([
                    'title' => $request->input('title', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'identifier' => $identifier,
                    'size' => $file->getSize(),
                    'attachable_id' => $request->input('attachable_id'),
                    'attachable_type' => $request->input('attachable_type'),
                    'file_type_uuid' => $request->input('file_type'),
                    'comment' => $request->input('comment'),
                    'uploaded_by' => Auth::user()->uuid,
                    'uploaded_at' => now(),
                ]);

                DB::commit();

                return new AttachmentResource(
                    $attachment->load(['uploadedBy', 'attachable', 'fileType'])
                );
            }
        } catch (Exception $e) {
            DB::rollBack();
            if (isset($filePath)) {
                FileHelper::delete($filePath);
            }
            throw $e;
        }
    }

    public function show(Attachment $attachment)
    {
        return [
            'attachment' => new AttachmentResource(
                $attachment->load(['uploadedBy', 'attachable', 'fileType'])
            )
        ];
    }

    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $attachments = Attachment::whereIn('id', $ids)->get();

            if ($attachments->isEmpty()) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            Attachment::whereIn('id', $attachments->pluck('id'))->delete();
            DB::commit();

            foreach ($attachments as $attachment) {
                FileHelper::delete("uploads/" . $attachment->identifier);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e->getCode() === "23000") {
                throw new \Exception(__('app/common.repository.foreignKey'));
            }

            throw new \Exception(__('app/common.repository.error'));
        }
    }
}
