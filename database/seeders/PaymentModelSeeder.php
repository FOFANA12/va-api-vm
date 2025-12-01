<?php

namespace Database\Seeders;

use App\Models\PaymentMode;
use Illuminate\Database\Seeder;

class PaymentModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentMode::whereNotNull('id')->delete();

        $paymentModes = [
            ['name' => "Espèces"],
            ['name' => "Carte de crédit"],
            ['name' => "Virement bancaire"],
            ['name' => "Mobile Money"],
            ['name' => "PayPal"],
        ];

        foreach ($paymentModes as $mode) {
            PaymentMode::create([
                'name' => $mode['name'],
                'status' => true,
                'created_by' => null,
                'updated_by' => null,
            ]);
        }
    }
}
