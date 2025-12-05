<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ElementaryLevel;
use App\Repositories\ElementaryLevelStateRepository;
use App\Support\ElementaryLevelState;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ElementaryLevelStateController extends Controller
{
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ElementaryLevelStateRepository $repository)
    {
        $this->messageSuccessUpdated = __('app/elementary_level.controller.message_success_state_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of states for a given elementary level.
     */
    public function index($elementaryLevelId)
    {
        return response()->json($this->repository->index($elementaryLevelId))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for elementary level.
     */
    public function requirements(ElementaryLevel $elementaryLevel)
    {
        return response()->json($this->repository->requirements($elementaryLevel))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created elementary level.
     */
    public function store(Request $request, ElementaryLevel $elementaryLevel)
    {
        $validStates = ElementaryLevelState::codes();

        $state = $request->input('state');

        if (!in_array($state, $validStates)) {
            throw new \Exception(__('app/elementary_level.request.invalid_state'));
        }

        if (!$state) {
            throw new \Exception(__('app/elementary_level.request.state'));
        }

        $result = $this->repository->store($request, $elementaryLevel);

        return response()->json(['message' => $this->messageSuccessUpdated, 'state' =>  $result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified elementary levels records.
     */
    public function destroy(Request $request, ElementaryLevel $elementaryLevel)
    {
        $state = $this->repository->destroy($request, $elementaryLevel);

        return response()->json(['message' => $this->messageSuccessDeleted, 'state' => $state])->setStatusCode(Response::HTTP_OK);
    }
}
