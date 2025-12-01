<?php

namespace App\Http\Controllers\Settings;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\BeneficiaryRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\Settings\BeneficiaryResource;
use App\Models\Beneficiary;
use App\Repositories\Settings\BeneficiaryRepository;

class BeneficiaryController extends Controller
{

    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(BeneficiaryRepository $repository)
    {
        $this->messageSuccessCreated = __('app/settings/beneficiary.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/settings/beneficiary.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }
    /**
     * Display a listing of beneficiaries.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, BeneficiaryResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, BeneficiaryResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created beneficiary.
     */
    public function store(BeneficiaryRequest $request)
    {
        $beneficiary = $this->repository->store($request);
        return response()->json(['message' => $this->messageSuccessCreated, 'beneficiary' => $beneficiary])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified beneficiary.
     */
    public function show(Beneficiary $beneficiary)
    {
        return response()->json($this->repository->show($beneficiary))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified beneficiary.
     */
    public function update(BeneficiaryRequest $request, Beneficiary $beneficiary)
    {

        $beneficiary= $this->repository->update($request, $beneficiary);
        return response()->json(['message' => $this->messageSuccessUpdated, 'beneficiary' => $beneficiary])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove one or multiple delegated beneficiaries.
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);
        return response()->json(['message' => $this->messageSuccessDeleted])->setStatusCode(Response::HTTP_OK);
    }
}
