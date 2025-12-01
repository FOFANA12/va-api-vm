<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\SupplierEvaluation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierEvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SupplierEvaluation::whereNotNull('id')->delete();

        $evaluations = SupplierEvaluation::factory()->count(15)->create();

        $supplierUuids = $evaluations->pluck('supplier_uuid')->unique();

        foreach ($supplierUuids as $uuid) {
            $supplier = Supplier::where('uuid', $uuid)->first();

            if ($supplier) {
                $avgScore = $supplier->evaluations()->avg('total_score');
                $supplier->update(['note' => round($avgScore, 2)]);
            }
        }
    }
}
