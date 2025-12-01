<?php

namespace App\Repositories;

use App\Http\Requests\SupplierEvaluationRequest;
use App\Http\Resources\SupplierEvaluationResource;
use App\Models\Supplier;
use App\Models\SupplierEvaluation;
use Carbon\Carbon;
use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SupplierEvaluationRepository
{
    /**
     * List supplier evaluation with pagination, filters, and sorting.
     */
    public function index(Request $request)
    {
        $sortable = ['score_delay', 'score_price', 'score_quality', 'total_score', 'evaluated_at', 'evaluated_by'];
        $supplierId = $request->input('supplierId');

        // $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = SupplierEvaluation::join('suppliers', 'supplier_evaluations.supplier_uuid', 'suppliers.uuid')
            ->select(
                'supplier_evaluations.id',
                'supplier_evaluations.uuid',
                'supplier_evaluations.score_delay',
                'supplier_evaluations.score_price',
                'supplier_evaluations.score_quality',
                'supplier_evaluations.total_score',
                'supplier_evaluations.evaluated_at',
            )
            ->where('suppliers.id', $supplierId);


        $query->orderBy("supplier_evaluations.$sortBy", $sortOrder);


        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }




    /**
     * Store a new supplier evaluation.
     */
    public function store(SupplierEvaluationRequest $request, Supplier $supplier)
    {
        DB::beginTransaction();
        try {

            $request->merge([
                'mode' => $request->input('mode', 'view'),
                "evaluated_by" => Auth::user()?->uuid,
                "evaluated_at" => Carbon::now(),
            ]);

            $supplierEvaluation = $supplier->evaluations()->create($request->only([
                "score_delay",
                "score_price",
                "score_quality",
                "comment",
                "evaluated_at",
                "evaluated_by",
            ]));

            $totalScore = round(
                (
                    ($request->input('score_delay') * 4) +
                    ($request->input('score_price') * 2) +
                    ($request->input('score_quality') * 4)
                ) / 10,
                2
            );

            $supplierEvaluation->update(['total_score' => $totalScore]);
            $avgScore = $supplier->evaluations()->avg('total_score');
            $supplier->update(['note' => round($avgScore, 2)]);

            DB::commit();

            return new SupplierEvaluationResource($supplierEvaluation->load('supplier'));
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show a specific supplier evaluation.
     */
    public function show(SupplierEvaluation $supplierEvaluation)
    {
        return ['supplier_evaluation' => new SupplierEvaluationResource($supplierEvaluation->load([
            'evaluatedBy',
            'supplier'
        ]))];
    }

    /**
     * Update an supplier evaluation.
     */
    public function update(SupplierEvaluationRequest $request, SupplierEvaluation $supplierEvaluation)
    {
        DB::beginTransaction();
        try {

            $request->merge([
                "evaluated_by" => Auth::user()?->uuid,
                "evaluated_at" => Carbon::now(),
            ]);

            $supplierEvaluation->fill($request->only([
                "score_delay",
                "score_price",
                "score_quality",
                "comment",
                "evaluated_at",
                "evaluated_by",
            ]))->save();

            $totalScore = round(
                (
                    ($request->input('score_delay') * 4) +
                    ($request->input('score_price') * 2) +
                    ($request->input('score_quality') * 4)
                ) / 10,
                2
            );


            $supplierEvaluation->update(['total_score' => $totalScore]);

            $supplier = Supplier::where('uuid', $supplierEvaluation->supplier_uuid)->first();
            $avgScore = $supplier->evaluations()->avg('total_score');
            $supplier->update(['note' => round($avgScore, 2)]);

            DB::commit();

            return (new SupplierEvaluationResource($supplierEvaluation->load(['evaluatedBy', 'supplier'])))->additional([
                'mode' => $request->input('mode', 'edit')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete multiple funding sources.
     */
    public function destroy(Request $request, Supplier $supplier)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        try {
            DB::transaction(function () use ($ids, $supplier) {
                $deleted = SupplierEvaluation::where('supplier_uuid', $supplier->uuid)
                    ->whereIn('id', $ids)
                    ->delete();

                if ($deleted === 0) {
                    throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
                }

                $avgScore = $supplier->evaluations()->avg('total_score') ?? 0;
                $supplier->update(['note' => round($avgScore, 2)]);
            });

            return [
                'supplier' => [
                    'id' => $supplier->id,
                    'uuid' => $supplier->uuid,
                    'note' => round($supplier->note, 2)
                ],
            ];
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            if ($e->getCode() === "23000") {
                throw new \Exception(__('app/common.repository.foreignKey'));
            }

            throw new \Exception(__('app/common.repository.error'));
        }
    }
}
