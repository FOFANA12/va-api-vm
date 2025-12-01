<?php

namespace App\Http\Controllers\Settings;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PaymentModeRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\Settings\PaymentModeResource;
use App\Models\PaymentMode;
use App\Repositories\Settings\PaymentModeRepository;

class PaymentModelController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(PaymentModeRepository $repository)
    {
        $this->messageSuccessCreated = __('app/settings/payment_mode.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/settings/payment_mode.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }
    /**
     * Display a listing of payment modes.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, PaymentModeResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, PaymentModeResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created payment mode.
     */
    public function store(PaymentModeRequest $request)
    {
        $paymentMode = $this->repository->store($request);
        return response()->json(['message' => $this->messageSuccessCreated, 'payment_mode' => $paymentMode])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified payment mode.
     */
    public function show(PaymentMode $payment_mode) 
    {
        return response()->json($this->repository->show($payment_mode))->setStatusCode(Response::HTTP_OK);
    }
    /**
    * Update the specified payment mode.
    */
    public function update(PaymentModeRequest $request, PaymentMode $payment_mode) 
    {
        $paymentMode = $this->repository->update($request, $payment_mode);
        return response()->json(['message' => $this->messageSuccessUpdated, 'payment_mode' => $paymentMode]);
    }

    /**
    * Remove the specified resource from storage.
    */
    public function destroy(Request $request)  
    {
        $this->repository->destroy($request);
        return response()->json(['message' => $this->messageSuccessDeleted])->setStatusCode(Response::HTTP_OK);
    }
}
