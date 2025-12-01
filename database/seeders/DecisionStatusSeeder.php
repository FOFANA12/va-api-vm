<?php

namespace Database\Seeders;

use App\Models\Decision;
use App\Models\DecisionStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DecisionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DecisionStatus::whereNotNull('id')->delete();

        $decision = Decision::first();

        $decisionStatuses = [
            [
                'status' => 'none',
                'status_date' => now(),
                'comment' => 'Décision créée mais non traitée.',
            ],
            [
                'status' => 'in_progress',
                'status_date' => now(),
                'comment' => 'Décision en cours de traitement.',
            ],
            [
                'status' => 'processed',
                'status_date' => now(),
                'comment' => 'Décision traitée.',
            ]
        ];

        foreach ($decisionStatuses as $status) {
            DecisionStatus::create([
                'status' => $status['status'],
                'status_date' => $status['status_date'],
                'comment' => $status['comment'],
                'decision_uuid' => $decision?->uuid,
                'created_by' => null,
                'updated_by' => null,
            ]);
        }
    }
}
