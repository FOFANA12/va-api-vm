<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\CurrencyRequest;
use App\Http\Resources\Settings\CurrencyResource;
use App\Models\Currency;
use App\Support\Currency as SupportCurrency;
use App\Repositories\Settings\CurrencyRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class CurrencyController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(CurrencyRepository $repository)
    {
        $this->messageSuccessCreated = __('app/settings/currency.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/settings/currency.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the currencies.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);

        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, CurrencyResource::class)
                ->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, CurrencyResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created currency.
     */
    public function store(CurrencyRequest $request)
    {
        $currency = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'currency' => $currency
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified currency.
     */
    public function show(Currency $currency)
    {
        return response()->json(
            $this->repository->show($currency)
        )->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Get the default currency.
     */
    public function getDefaultCurrency()
    {
        $currency = SupportCurrency::getDefault();

        return response()->json(['currency' => $currency])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified currency.
     */
    public function update(CurrencyRequest $request, Currency $currency)
    {
        $currency = $this->repository->update($request, $currency);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'currency' => $currency
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified currency(ies).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
