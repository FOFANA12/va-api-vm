<?php

namespace App\Repositories\Report;

use App\Models\Action;
use App\Models\ActionMetric;
use App\Models\Indicator;
use App\Models\IndicatorCategory;
use App\Models\StrategicMap;
use App\Models\StrategicObjective;
use App\Models\Structure;
use App\Support\ActionState;
use App\Support\ActionStatus;
use App\Support\Currency;
use App\Support\IndicatorStatus;
use App\Support\StrategicObjectiveStatus;
use App\Support\StrategicState;
use Illuminate\Support\Facades\DB;

class DashboardReportRepository
{
    public function getGeneralDahsboard(Structure $structure): array
    {
        $structures[] = $structure->uuid;
        $collect = function ($s) use (&$structures, &$collect) {
            foreach ($s->children as $child) {
                if ($child->status) {
                    $structures[] = $child->uuid;
                }
                $collect($child);
            }
        };
        $collect($structure);

        $structureUuids = collect($structures)->pluck('uuid');
        $structureCount = count($structureUuids);

        $strategicMaps = StrategicMap::with(['elements.objectives', 'objectives'])
            ->whereIn('structure_uuid', $structureUuids)
            ->where('status', true)
            ->get();

        $structureMapsCount = $strategicMaps->count();
        $strategicAxesCount = $strategicMaps->sum(fn($map) => $map->elements->count());

        $leadObjectivesCount = StrategicObjective::where('lead_structure_uuid', $structure->uuid)->count();

        $actions = Action::whereIn('structure_uuid', $structureUuids)->get();
        $actionsCount = $actions->count();
        $actionUuids = $actions->pluck('uuid');

        $metrics = ActionMetric::whereIn('action_uuid', $actionUuids)->get();

        $realizationRate = $metrics->avg('realization_rate') ?? 0;
        $realizationIndex = $metrics->avg('realization_index') ?? 0;

        $disbursementRates = $actions->map(function ($action) {
            if ($action->total_receipt_fund > 0) {
                return ($action->total_disbursement_fund / $action->total_receipt_fund) * 100;
            }
            return 0;
        });
        $disbursementRate = $disbursementRates->avg() ?? 0;

        $failedObjectivesCount = StrategicObjective::whereIn('structure_uuid', $structureUuids)
            ->where('failed', true)
            ->count();

        $alertObjectivesCount = StrategicObjective::whereIn('structure_uuid', $structureUuids)
            ->where('alert', true)
            ->count();

        $failedActionsCount = Action::whereIn('structure_uuid', $structureUuids)
            ->where('failed', true)
            ->count(); // action en vain == no alignment

        $alertActionsCount = Action::whereIn('structure_uuid', $structureUuids)
            ->where('alert', true)
            ->count();


        $objectives = StrategicObjective::where('lead_structure_uuid', $structure->uuid)->get();

        $locale = app()->getLocale();
        $objectivesStatusDistribution = collect(StrategicObjectiveStatus::codes())
            ->map(function ($code) use ($objectives, $leadObjectivesCount, $locale) {

                $count = $objectives->where('status', $code)->count();

                return [
                    'code'  => $code,
                    'name'  => StrategicObjectiveStatus::name($code, $locale),
                    'color' => StrategicObjectiveStatus::get($code, $locale)->color,
                    'value' => $leadObjectivesCount > 0
                        ? round(($count / $leadObjectivesCount) * 100, 2)
                        : 0,
                ];
            })
            ->values();

        return [
            'structures_active_count' => $structureCount,
            'strategic_maps_active_count' => $structureMapsCount,
            'strategic_axes_count' => $strategicAxesCount,
            'lead_objectives_count' => $leadObjectivesCount,

            'actions_count' => $actionsCount,
            'realization_rate' => round($realizationRate, 2),
            'disbursement_rate' => round($disbursementRate, 2),
            'realization_index' => round($realizationIndex, 2),

            'failed_objectives_count' => $failedObjectivesCount,
            'alert_objectives_count' => $alertObjectivesCount,

            'failed_actions_count' => $failedActionsCount,
            'alert_actions_count' => $alertActionsCount,

            'objectives_status_distribution' => $objectivesStatusDistribution,
        ];
    }

    public function getStrategicDashboard(Structure $structure): array
    {
        $locale = app()->getLocale();
        $objectives = StrategicObjective::where('lead_structure_uuid', $structure->uuid)->get();
        $totalObjectives = $objectives->count();


        $failedObjectivesCount = $objectives->where('failed', true)->count();
        $alertObjectivesCount  = $objectives->where('alert', true)->count();

        $objectivesStatusDistribution = collect(StrategicObjectiveStatus::codes())
            ->map(function ($code) use ($objectives, $totalObjectives, $locale) {

                $count = $objectives->where('status', $code)->count();

                return [
                    'code' => $code,
                    'name' => StrategicObjectiveStatus::name($code, $locale),
                    'color' => StrategicObjectiveStatus::get($code, $locale)->color,
                    'count' => $count,
                    'percent' => $totalObjectives > 0
                        ? round(($count / $totalObjectives) * 100, 2)
                        : 0,
                ];
            })
            ->values();

        $objectivesStateDistribution = collect(StrategicState::codes())
            ->map(function ($code) use ($objectives, $totalObjectives, $locale) {

                $count = $objectives->where('state', $code)->count();

                return [
                    'code' => $code,
                    'name' => StrategicState::name($code, $locale),
                    'color' => StrategicState::get($code, $locale)->color,
                    'count' => $count,
                    'percent' => $totalObjectives > 0
                        ? round(($count / $totalObjectives) * 100, 2)
                        : 0,
                ];
            })
            ->values();


        $indicators = Indicator::where('structure_uuid', $structure->uuid)->get();
        $totalIndicators = $indicators->count();

        $indicatorStatusDistribution = collect(IndicatorStatus::codes())
            ->map(function ($code) use ($indicators, $totalIndicators, $locale) {

                $count = $indicators->where('status', $code)->count();

                return [
                    'code' => $code,
                    'name' => IndicatorStatus::name($code, $locale),
                    'color' => IndicatorStatus::get($code, $locale)->color,
                    'count' => $count,
                    'percent' => $totalIndicators > 0
                        ? round(($count / $totalIndicators) * 100, 2)
                        : 0,
                ];
            })
            ->values();

        $indicatorStateDistribution = collect(StrategicState::codes())
            ->map(function ($code) use ($indicators, $totalIndicators, $locale) {
                $count = $indicators->where('state', $code)->count();

                return [
                    'code' => $code,
                    'name' => StrategicState::name($code, $locale),
                    'color' => StrategicState::get($code, $locale)->color,
                    'count' => $count,
                    'percent' => $totalIndicators > 0
                        ? round(($count / $totalIndicators) * 100, 2)
                        : 0,
                ];
            })
            ->values();


        $categoryUuids = $indicators->pluck('category_uuid')->unique();
        $categories = IndicatorCategory::whereIn('uuid', $categoryUuids)->get()->keyBy('uuid');

        $indicatorCategoryDistribution = $indicators
            ->groupBy('category_uuid')
            ->map(function ($items, $categoryUuid) use ($categories, $totalIndicators) {

                $count = $items->count();
                $category = $categories->get($categoryUuid);

                return [
                    'category_uuid' => $categoryUuid,
                    'name' => $category->name,
                    'count' => $count,
                    'percent' => $totalIndicators > 0
                        ? round(($count / $totalIndicators) * 100, 2)
                        : 0,
                ];
            })
            ->values();

        return [
            'lead_objectives_count' => $totalObjectives,

            'failed_objectives_count' => $failedObjectivesCount,
            'alert_objectives_count'  => $alertObjectivesCount,

            'objectives_status_distribution' => $objectivesStatusDistribution,
            'objectives_state_distribution' => $objectivesStateDistribution,


            'indicators_count' => $totalIndicators,
            'indicators_status_distribution' => $indicatorStatusDistribution,
            'indicators_state_distribution'  => $indicatorStateDistribution,

            'indicators_category_distribution' => $indicatorCategoryDistribution,
        ];
    }

    public function getOperationalDashboard(Structure $structure): array
    {
        $structures[] = $structure->uuid;
        $collect = function ($s) use (&$structures, &$collect) {
            foreach ($s->children as $child) {
                if ($child->status) {
                    $structures[] = $child->uuid;
                }
                $collect($child);
            }
        };
        $collect($structure);

        $structureUuids = collect($structures);

        $actions = Action::whereIn('structure_uuid', $structureUuids)->get();
        $actionsCount = $actions->count();

        $actionUuids = $actions->pluck('uuid');

        $metrics = ActionMetric::whereIn('action_uuid', $actionUuids)->get();

        $averageRealizationRate = $metrics->avg('realization_rate') ?? 0;
        $averageRealizationIndex = $metrics->avg('realization_index') ?? 0;

        $disbursementRates = $actions->map(function ($action) {
            if ($action->total_receipt_fund > 0) {
                return ($action->total_disbursement_fund / $action->total_receipt_fund) * 100;
            }
            return null;
        })->filter();

        $averageDisbursementRate = $disbursementRates->avg() ?? 0;

        $failedActionsCount = $actions->where('failed', true)->count();
        $alertActionsCount  = $actions->where('alert', true)->count();

        $locale = app()->getLocale();
        $actionStatusDistribution = collect(ActionStatus::codes())
            ->map(function ($code) use ($actions, $actionsCount, $locale) {

                $count = $actions->where('status', $code)->count();

                return [
                    'code' => $code,
                    'name' => ActionStatus::name($code, $locale),
                    'color' => ActionStatus::get($code, $locale)->color,
                    'count' => $count,
                    'percent' => $actionsCount > 0
                        ? round(($count / $actionsCount) * 100, 2)
                        : 0,
                ];
            })
            ->values();


        $actionStateDistribution = collect(ActionState::codes())
            ->map(function ($code) use ($actions, $actionsCount, $locale) {
                $count = $actions->where('state', $code)->count();

                return [
                    'code' => $code,
                    'name' => ActionState::name($code, $locale),
                    'color' => ActionState::get($code, $locale)->color,
                    'count' => $count,
                    'percent' => $actionsCount > 0
                        ? round(($count / $actionsCount) * 100, 2)
                        : 0,
                ];
            })
            ->values();

        return [
            'actions_count' => $actionsCount,

            'realization_rate'   => round($averageRealizationRate, 2),
            'disbursement_rate'  => round($averageDisbursementRate, 2),
            'realization_index'  => round($averageRealizationIndex, 2),

            'failed_actions_count' => $failedActionsCount,
            'alert_actions_count'  => $alertActionsCount,

            'actions_status_distribution' => $actionStatusDistribution,
            'actions_state_distribution'  => $actionStateDistribution,
        ];
    }

    public function getFinancialDashboard(Structure $structure): array
    {

        $structures[] = $structure->uuid;
        $collect = function ($s) use (&$structures, &$collect) {
            foreach ($s->children as $child) {
                if ($child->status) {
                    $structures[] = $child->uuid;
                }
                $collect($child);
            }
        };
        $collect($structure);

        $structureUuids = collect($structures);


        $actions = Action::whereIn('structure_uuid', $structureUuids)->get();
        $actionUuids = $actions->pluck('uuid');

        $plannedBudget = $actions->sum('total_budget');
        $acquiredBudget = $actions->sum('total_receipt_fund');
        $spentBudget = $actions->sum('total_disbursement_fund');

        $budgetToMobilize = max($plannedBudget - $acquiredBudget, 0);
        $availableBudget  = max($acquiredBudget - $spentBudget, 0);

        $disbursementRate = $acquiredBudget > 0
            ? round(($spentBudget / $acquiredBudget) * 100, 2)
            : 0;

        $disbursementByExpenseTypes = DB::table('action_fund_disbursement_expense_types as afdet')
            ->join('action_fund_disbursements as afd', 'afd.uuid', '=', 'afdet.action_fund_disbursement_uuid')
            ->join('expense_types as et', 'afdet.expense_type_uuid', '=', 'et.uuid')
            ->select('et.name as type', DB::raw('SUM(DISTINCT afd.payment_amount) as total'))
            ->whereIn('afd.action_uuid', $actionUuids)
            ->groupBy('et.uuid', 'et.name')
            ->get()
            ->map(function ($row) use ($spentBudget) {
                return [
                    'type' => $row->type,
                    'total' => (float) $row->total,
                    'percent' => $spentBudget > 0
                        ? round(($row->total / $spentBudget) * 100, 2)
                        : 0,
                ];
            });

        $disbursementByBudgetTypes = DB::table('action_fund_disbursements as afd')
            ->join('budget_types as bt', 'afd.budget_type_uuid', '=', 'bt.uuid')
            ->whereIn('afd.action_uuid', $actionUuids)
            ->select(
                'bt.uuid',
                'bt.name',
                DB::raw('SUM(afd.payment_amount) as total')
            )
            ->groupBy('bt.uuid', 'bt.name')
            ->get()
            ->map(function ($row) use ($spentBudget) {
                return [
                    'type' => $row->name,
                    'total' => (float) $row->total,
                    'percent' => $spentBudget > 0
                        ? round(($row->total / $spentBudget) * 100, 2)
                        : 0,
                ];
            });


        return [
            'planned_budget' => $plannedBudget,
            'acquired_budget' => $acquiredBudget,
            'spent_budget' => $spentBudget,

            'budget_to_mobilize' => $budgetToMobilize,
            'available_budget' => $availableBudget,

            'disbursement_rate' => $disbursementRate,

            'disbursement_by_expense_types' => $disbursementByExpenseTypes,
            'disbursement_by_budget_types' => $disbursementByBudgetTypes,
            'currency' => Currency::getDefault(app()->getLocale())['code'],
        ];
    }
}
