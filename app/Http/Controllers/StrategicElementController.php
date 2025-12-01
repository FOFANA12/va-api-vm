<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\StrategicElementRequest;
use App\Http\Resources\StrategicElementResource;
use App\Models\StrategicElement;
use App\Repositories\StrategicElementRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class StrategicElementController extends Controller
{
    use ApiResponse;

    private $messageSuccessDeleted;
    private $repository;

    public function __construct(StrategicElementRepository $repository)
    {
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the strategic elements.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, StrategicElementResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, StrategicElementResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for strategic element.
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created strategic element.
     */
    public function store(StrategicElementRequest $request)
    {
        $strategicElement = $this->repository->store($request);
        $messageKey = $strategicElement->type === 'LEVER'
            ? 'app/strategic_element.controller.lever_created'
            : 'app/strategic_element.controller.axis_created';

        return response()->json(['message' => __($messageKey), 'strategic_element' =>  $strategicElement])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified strategic element.
     */
    public function show(StrategicElement $strategicElement)
    {
        return response()->json($this->repository->show($strategicElement))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified strategic element.
     */
    public function update(StrategicElementRequest $request, StrategicElement $strategicElement)
    {
        $updatedStrategicElement = $this->repository->update($request, $strategicElement);

        $messageKey = $strategicElement->type === 'LEVER'
            ? 'app/strategic_element.controller.lever_updated'
            : 'app/strategic_element.controller.axis_updated';

        return response()->json(['message' => __($messageKey), 'strategic_element' =>  $updatedStrategicElement])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified or multiple structure strategic element.
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json(['message' => $this->messageSuccessDeleted])->setStatusCode(Response::HTTP_OK);
    }
}
