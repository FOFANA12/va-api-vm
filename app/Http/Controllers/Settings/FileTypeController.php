<?php

namespace App\Http\Controllers\Settings;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\FileTypeRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\Settings\FileTypeResource;
use App\Models\FileType;
use App\Repositories\Settings\FileTypeRepository;
use Illuminate\Support\Facades\Storage;

class FileTypeController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(FileTypeRepository $repository)
    {
        $this->messageSuccessCreated = __('app/settings/file_type.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/settings/file_type.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the file types.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);

        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, FileTypeResource::class)
                ->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, FileTypeResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created file type.
     */
    public function store(FileTypeRequest $request)
    {
        $fileType = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'file_type' => $fileType
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified file type.
     */
    public function show(FileType $fileType)
    {
        return response()->json(
            $this->repository->show($fileType)
        )->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified file type.
     */
    public function update(FileTypeRequest $request, FileType $fileType)
    {
        $fileType = $this->repository->update($request, $fileType);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'file_type' => $fileType
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified file type(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Download an file type attachment.
     *
     */
    public function download(FileType $fileType)
    {
        $disk = env('FILESYSTEM_DISK', 'local');
        $filePath = "uploads/{$fileType->identifier}";

        if (!Storage::disk($disk)->exists($filePath)) {
            return response()->json([
                'message' => __('app/settings/file_type.validation.file_not_found')
            ], Response::HTTP_NOT_FOUND);
        }

        $originalName = $fileType->original_name;
        $extension = pathinfo($fileType->identifier, PATHINFO_EXTENSION);

        if (!str_ends_with($originalName, ".{$extension}")) {
            $originalName .= ".{$extension}";
        }

        return Storage::disk($disk)->download($filePath, $originalName);
    }
}
