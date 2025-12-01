<?php

namespace App\Http\Controllers\Settings;

use App\Models\DefaultPhase;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\DefaultPhaseRequest;
use App\Repositories\Settings\DefaultPhaseRepository;

class DefaultPhaseController extends Controller
{
    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(DefaultPhaseRepository $repository)
    {
        $this->messageSuccessCreated = __('app/settings/default_phase.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/settings/default_phase.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display all default phases.
     */
    public function index(DefaultPhase $defaultPhase)
    {
        return response()->json(
            $this->repository->index($defaultPhase),
            Response::HTTP_OK
        );
    }

    /**
     * Requirements data for default phases.
     */
    public function requirements()
    {
        return response()->json(
            $this->repository->requirements(),
            Response::HTTP_OK
        );
    }

    /**
     * Show a specific default phase.
     */
    public function show(DefaultPhase $defaultPhase)
    {
        return response()->json(
            $this->repository->show($defaultPhase),
            Response::HTTP_OK
        );
    }

    /**
     * Store a new default phase.
     */
    public function store(DefaultPhaseRequest $request)
    {
        $defaultPhase = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'default_phase' => $defaultPhase
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified default phase.
     */
    public function update(DefaultPhaseRequest $request, DefaultPhase $defaultPhase)
    {
        $defaultPhase = $this->repository->update($request, $defaultPhase);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'default_phase'   => $defaultPhase
        ], Response::HTTP_OK);
    }

    /**
     * Delete one default phase.
     */
    public function destroy(DefaultPhase $defaultPhase)
    {
        $this->repository->destroy($defaultPhase);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
