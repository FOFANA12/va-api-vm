<?php

namespace App\Repositories\Report;

use App\Models\StrategicDomain;
use App\Models\CapabilityDomain;
use App\Models\ElementaryLevel;
use App\Models\Action;
use App\Support\Currency;
use Illuminate\Support\Facades\DB;

class StrategicDomainReportRepository
{
    public function getGlobalReport(StrategicDomain $strategicDomain): array
    {
        return $this->buildReport($strategicDomain);
    }

    private function buildReport(StrategicDomain $strategicDomain): array
    {
        // COUNTS
        $capabilityIds = CapabilityDomain::where('strategic_domain_uuid', $strategicDomain->uuid)->pluck('uuid');
        $elementaryIds = ElementaryLevel::whereIn('capability_domain_uuid', $capabilityIds)->pluck('uuid');

        $capabilityCount = $capabilityIds->count();
        $elementaryCount = $elementaryIds->count();

        // ACTIONS
        $actions = Action::where('strategic_domain_uuid', $strategicDomain->uuid)->get();
        $totalActions = $actions->count();

        // Eviter division par zéro
        $safeActionCount = max($totalActions, 1);

        // INIT
        $totalPlannedBudget = 0;
        $totalAcquiredBudget = 0;
        $totalSpentBudget = 0;

        $totalDisbursementRate = 0;
        $totalRealisationRate = 0;
        $totalPerformanceIndex = 0;

        // LOOP actions
        foreach ($actions as $action) {
            $planned = (float) $action->total_budget;
            $acquired = (float) $action->total_receipt_fund;
            $spent = (float) $action->total_disbursement_fund;
            $realisedPercent = (float) $action->actual_progress_percent;

            // Budgets cumulés
            $totalPlannedBudget += $planned;
            $totalAcquiredBudget += $acquired;
            $totalSpentBudget += $spent;

            // Taux de décaissement
            $disbursementRate = $acquired > 0
                ? round(($spent / $acquired) * 100, 2)
                : 0;

            $totalDisbursementRate += $disbursementRate;

            // Taux de réalisation
            $totalRealisationRate += $realisedPercent;

            // Indice de performance
            $plannedPercent = $action->is_planned ? 100 : 0;

            $performanceIndex = $plannedPercent > 0
                ? round($realisedPercent / $plannedPercent, 2)
                : 0;

            $totalPerformanceIndex += $performanceIndex;
        }

        // CALCULS BUDGÉTAIRES
        $budgetToMobilize = max($totalPlannedBudget - $totalAcquiredBudget, 0);
        $availableBudget = max($totalAcquiredBudget - $totalSpentBudget, 0);

        // CALCULS DES MOYENNES
        $averageDisbursementRate = round($totalDisbursementRate / $safeActionCount, 2);
        $averageRealisationRate  = round($totalRealisationRate  / $safeActionCount, 2);
        $averagePerformanceIndex = round($totalPerformanceIndex / $safeActionCount, 2);

        // DÉCAISSEMENT PAR TYPE
        $expenseTypes = DB::table('action_fund_disbursement_expense_types as afdet')
            ->join('action_fund_disbursements as afd', 'afd.uuid', '=', 'afdet.action_fund_disbursement_uuid')
            ->join('actions as a', 'a.uuid', '=', 'afd.action_uuid')
            ->join('expense_types as et', 'afdet.expense_type_uuid', '=', 'et.uuid')
            ->select('et.name as type', DB::raw('SUM(DISTINCT afd.payment_amount) as total'))
            ->where('a.strategic_domain_uuid', $strategicDomain->uuid)
            ->groupBy('et.uuid', 'et.name')
            ->get()
            ->map(function ($row) use ($totalSpentBudget) {
                return [
                    'type'    => $row->type,
                    'total'   => (float) $row->total,
                    'percent' => $totalSpentBudget > 0
                        ? round(($row->total / $totalSpentBudget) * 100, 2)
                        : 0,
                ];
            });

        return [
            'counts' => [
                'capability_domains' => $capabilityCount,
                'elementary_levels' => $elementaryCount,
                'actions' => $totalActions,
            ],

            'budget' => [
                'planned_budget' => $totalPlannedBudget,
                'acquired_budget' => $totalAcquiredBudget,
                'spent_budget' => $totalSpentBudget,
                'budget_to_mobilize' => $budgetToMobilize,
                'available_budget' => $availableBudget,
                'disbursement_types' => $expenseTypes,
                'currency' => $domain->currency ?? 'MRU',

                'total_disbursement_rate' => $totalDisbursementRate,
                'total_realisation_rate' => $totalRealisationRate,
                'total_performance_index' => $totalPerformanceIndex,

                'average_disbursement_rate' => $averageDisbursementRate,
                'average_realisation_rate' => $averageRealisationRate,
                'average_performance_index' => $averagePerformanceIndex,
            ],
        ];
    }

    public function buildGeneralDashboard(): array
    {
        // COUNTS
        $strategicCount = StrategicDomain::count();
        $capabilityCount = CapabilityDomain::count();
        $elementaryCount = ElementaryLevel::count();

        // ACTIONS liées à un strategic domain
        $actions = Action::whereNotNull('strategic_domain_uuid')->get();
        $totalActions = $actions->count();

        $safeActionCount = max($totalActions, 1);

        // INIT
        $totalPlannedBudget = 0;
        $totalAcquiredBudget = 0;
        $totalSpentBudget = 0;

        $totalDisbursementRate = 0;
        $totalRealisationRate = 0;
        $totalPerformanceIndex = 0;

        // LOOP actions
        foreach ($actions as $action) {
            $planned = (float) $action->total_budget;
            $acquired = (float) $action->total_receipt_fund;
            $spent = (float) $action->total_disbursement_fund;
            $realisedPercent = (float) $action->actual_progress_percent;

            $totalPlannedBudget += $planned;
            $totalAcquiredBudget += $acquired;
            $totalSpentBudget += $spent;

            $disbursementRate = $acquired > 0
                ? round(($spent / $acquired) * 100, 2)
                : 0;

            $totalDisbursementRate += $disbursementRate;
            $totalRealisationRate += $realisedPercent;

            $plannedPercent = $action->is_planned ? 100 : 0;

            $performanceIndex = $plannedPercent > 0
                ? round($realisedPercent / $plannedPercent, 2)
                : 0;

            $totalPerformanceIndex += $performanceIndex;
        }

        // BUDGETS
        $budgetToMobilize = max($totalPlannedBudget - $totalAcquiredBudget, 0);
        $availableBudget = max($totalAcquiredBudget - $totalSpentBudget, 0);

        // AVERAGES
        $averageDisbursementRate = round($totalDisbursementRate / $safeActionCount, 2);
        $averageRealisationRate = round($totalRealisationRate / $safeActionCount, 2);
        $averagePerformanceIndex = round($totalPerformanceIndex / $safeActionCount, 2);

        // DISBURSEMENT BY TYPE
        $budgetTypes = DB::table('action_fund_disbursements as afd')
            ->join('actions as a', 'a.uuid', '=', 'afd.action_uuid')
            ->join('budget_types as bt', 'afd.budget_type_uuid', '=', 'bt.uuid')
            ->whereNotNull('a.strategic_domain_uuid')
            ->select(
                'bt.uuid',
                'bt.name',
                DB::raw('SUM(afd.payment_amount) as total')
            )
            ->groupBy('bt.uuid', 'bt.name')
            ->get()
            ->map(function ($row) use ($totalSpentBudget) {
                return [
                    'type' => $row->name,
                    'total' => (float) $row->total,
                    'percent' => $totalSpentBudget > 0
                        ? round(($row->total / $totalSpentBudget) * 100, 2)
                        : 0,
                ];
            });


        return [
            'counts' => [
                'strategic_domains' => $strategicCount,
                'capability_domains' => $capabilityCount,
                'elementary_levels' => $elementaryCount,
                'actions' => $totalActions,
            ],

            'budget' => [
                'planned_budget' => $totalPlannedBudget,
                'acquired_budget' => $totalAcquiredBudget,
                'spent_budget' => $totalSpentBudget,
                'budget_to_mobilize' => $budgetToMobilize,
                'available_budget' => $availableBudget,
                'budget_types' => $budgetTypes,
                'currency' => Currency::getDefault(app()->getLocale())['code'],

                'total_disbursement_rate' => $totalDisbursementRate,
                'total_realisation_rate' => $totalRealisationRate,
                'total_performance_index' => $totalPerformanceIndex,

                'disbursement_rate' => $averageDisbursementRate,
                'realisation_rate' => $averageRealisationRate,
                'performance_index' => $averagePerformanceIndex,
            ],
        ];
    }
}
