<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponse
{
    /**
     * Return a paginated response in the format { data, meta }.
     *
     * @param  LengthAwarePaginator  $paginator
     * @param  string|JsonResource   $resourceClass
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithPagination(LengthAwarePaginator $paginator, string $resourceClass)
    {
        $data = $resourceClass::collection($paginator->items())->resolve(request());

        $meta = [
            'current_page' => $paginator->currentPage(),
            'from' => $paginator->firstItem(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
        ];

        return response()->json([
            'data' => $data,
            'meta' => $meta,
        ]);
    }

    /**
     * Return a non-paginated response in the format { data }.
     *
     * @param  iterable              $collection
     * @param  string|JsonResource   $resourceClass
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithCollection(iterable $collection, string $resourceClass)
    {
        $data = $resourceClass::collection($collection)->resolve(request());

        $meta = [
            'current_page' => 1,
            'from' => 1,
            'last_page' => 1,
            'per_page' => -1,
            'to' => count($data),
            'total' => count($data),
        ];

        return response()->json([
            'data' => $data,
            'meta' => $meta,
        ]);
    }
}
