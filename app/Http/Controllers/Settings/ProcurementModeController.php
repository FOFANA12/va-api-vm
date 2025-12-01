<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProcurementModeRequest;
use App\Http\Resources\Settings\ProcurementModeResource;
use App\Models\ProcurementMode;
use App\Repositories\Settings\ProcurementModeRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class ProcurementModeController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ProcurementModeRepository $repository)
    {
        $this->messageSuccessCreated = __('app/settings/procurement_mode.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/settings/procurement_mode.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of procurement modes.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);

        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, ProcurementModeResource::class)
                        ->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, ProcurementModeResource::class)
                    ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Fetch requirements for procurement mode creation/update.
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created procurement mode.
     */
    public function store(ProcurementModeRequest $request)
    {
        $procurementMode = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'procurement_mode' => $procurementMode
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified procurement mode.
     */
    public function show(ProcurementMode $procurementMode)
    {
        return response()->json(
            $this->repository->show($procurementMode)
        )->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified procurement mode.
     */
    public function update(ProcurementModeRequest $request, ProcurementMode $procurementMode)
    {
        $procurementMode = $this->repository->update($request, $procurementMode);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'procurement_mode' => $procurementMode
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove one or multiple procurement modes.
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
