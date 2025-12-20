<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActionRequest;
use App\Http\Resources\ActionResource;
use App\Models\Action;
use App\Repositories\ActionRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class ActionController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ActionRepository $repository)
    {
        $this->messageSuccessCreated = __('app/action.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/action.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the actions.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, ActionResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, ActionResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for action.
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created action.
     */
    public function store(ActionRequest $request)
    {
        $action = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'action' => $action
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified action.
     */
    public function show(Action $action)
    {
        return response()->json($this->repository->show($action))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified action.
     */
    public function update(ActionRequest $request, Action $action)
    {
        $action = $this->repository->update($request, $action);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'action' => $action
        ])->setStatusCode(Response::HTTP_OK);
    }


    /**
     * Remove the specified action(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }

    public function downloadSelectionMode()
    {
        $filePath = public_path('storage/templates/selection-mode.pdf');

        if (!file_exists($filePath)) {
            return response()->json([
                'message' => 'Fichier introuvable.'
            ], Response::HTTP_NOT_FOUND
        );
        }

        return response()->download($filePath, 'selection-modes.pdf');
    }
}
