<?php

namespace App\Repositories\Settings;

use App\Helpers\FileHelper;
use RuntimeException;
use App\Models\FileType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Settings\FileTypeRequest;
use App\Http\Resources\Settings\FileTypeResource;

class FileTypeRepository
{
    /**
     * List file types with pagination, filters, and sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['name', 'original_name', 'status'];
        $sortable = ['name', 'original_name', 'status'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'file_types.id';

        $query = FileType::select(
            'file_types.id',
            'file_types.uuid',
            'file_types.name',
            'file_types.original_name',
            'file_types.status',
        );

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {

                    $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                }
            });
        }


        $query->orderBy($sortBy, $sortOrder);


        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Store a new file type.
     */
    public function store(FileTypeRequest $request)
    {
        $identifier = null;

        DB::beginTransaction();
        try {

            $request->merge([
                "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
                "created_by" => Auth::user()?->uuid,
                "updated_by" => Auth::user()?->uuid,
            ]);

            $data = $request->only([
                "name",
                "status",
                "created_by",
                "updated_by",
            ]);

            if ($request->hasFile("file")) {
                $file = $request->file("file");

                $identifier = FileHelper::upload($file, "uploads");

                $data["original_name"] = $file->getClientOriginalName();
                $data["identifier"] = $identifier;
                $data["mime_type"] = $file->getMimeType();
                $data["size"] = $file->getSize();
            }

            $fileType = FileType::create($data);

            DB::commit();

            return new FileTypeResource($fileType);
        } catch (\Exception $e) {
            DB::rollBack();

            if (!empty($identifier)) {
                FileHelper::delete("uploads/{$identifier}");
            }

            throw $e;
        }
    }

    public function show(FileType $fileType)
    {
        return ['file_type' => new FileTypeResource($fileType)];
    }

    public function update(FileTypeRequest $request, FileType $fileType)
    {
        $oldFile = $fileType->identifier;
        $newFile = null;

        DB::beginTransaction();

        try {
            $request->merge([
                "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
                "updated_by" => Auth::user()?->uuid,
            ]);

            $data = $request->only([
                "name",
                "status",
                "updated_by",
            ]);

            if ($request->boolean('delete_file') && $oldFile) {
                FileHelper::delete("uploads/{$oldFile}");
                $fileType->original_name = null;
                $fileType->identifier = null;
                $fileType->mime_type = null;
                $fileType->size = null;
            }

            if ($request->hasFile("file")) {
                $file = $request->file("file");
                $newFile = FileHelper::upload($file, "uploads");

                $data["original_name"] = $file->getClientOriginalName();
                $data["identifier"] = $newFile;
                $data["mime_type"] = $file->getMimeType();
                $data["size"] = $file->getSize();

                if ($oldFile && $oldFile !== $newFile) {
                    FileHelper::delete("uploads/{$oldFile}");
                }
            }

            $fileType->fill($data)->save();

            DB::commit();

            return new FileTypeResource($fileType);
        } catch (\Exception $e) {
            DB::rollBack();
            if (!empty($newFile)) {
                FileHelper::delete("uploads/{$newFile}");
            }
            throw $e;
        }
    }

    /**
     * Delete multiple file types.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $fileTypes = FileType::whereIn('id', $ids)->get();

            if ($fileTypes->isEmpty()) {
                throw new RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $filesToDelete = $fileTypes
                ->pluck('identifier')
                ->filter()
                ->toArray();

            foreach ($fileTypes as $fileType) {
                $fileType->delete();
            }

            DB::commit();

            foreach ($filesToDelete as $identifier) {
                FileHelper::delete("uploads/{$identifier}");
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
