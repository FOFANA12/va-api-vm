<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttachmentRequest;
use App\Http\Resources\AttachmentResource;
use App\Models\Attachment;
use App\Repositories\AttachmentRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(AttachmentRepository $repository)
    {
        $this->messageSuccessCreated = __('app/attachment.controller.message_success_uploaded');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the attachments.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);

        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, AttachmentResource::class)
                ->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, AttachmentResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for action.
     */
    public function requirements()
    {
        return response()->json($this->repository->requirements())->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created attachment (file upload).
     */
    public function store(AttachmentRequest $request)
    {
        $attachment = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'attachment' => $attachment
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified attachment.
     */
    public function show(Attachment $attachment)
    {
        return response()->json($this->repository->show($attachment))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Download an attachment securely.
     *
     * @param string $uuid
     * @return BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function download(Attachment $attachment)
    {
        $disk = env('FILESYSTEM_DISK', 'local');
        $filePath = "uploads/{$attachment->identifier}";

        if (!Storage::disk($disk)->exists($filePath)) {
            return response()->json([
                'message' => __('app/attachment.validation.file_not_found')
            ], Response::HTTP_NOT_FOUND);
        }

        $originalName = $attachment->original_name;
        $extension = pathinfo($attachment->identifier, PATHINFO_EXTENSION);

        if (!str_ends_with($originalName, ".{$extension}")) {
            $originalName .= ".{$extension}";
        }

        return Storage::disk($disk)->download($filePath, $originalName);
    }

    /**
     * Remove the specified attachment(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted,
        ])->setStatusCode(Response::HTTP_OK);
    }
}
