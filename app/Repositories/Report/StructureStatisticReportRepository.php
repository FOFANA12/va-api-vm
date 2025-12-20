<?php

namespace App\Repositories\Report;

use App\Models\ActionFundDisbursement;
use App\Models\Structure;
use Illuminate\Support\Facades\DB;

class StructureStatisticReportRepository
{

    /**
     * Acquisition report grouped by funding source and by currency.
     */
    public function getAcquisitionReport(Structure $structure): array
    {

        $structures = [];
        $collect = function ($s) use (&$structures, &$collect) {
            foreach ($s->children as $child) {
                if ($child->status) {
                    $structures[] = $child->uuid;
                }
                $collect($child);
            }
        };
        $collect($structure);

        // Total acquisitions (converted_amount)
        $total = DB::table('action_fund_receipts')
            ->join('actions', 'actions.uuid', '=', 'action_fund_receipts.action_uuid')
            ->join('action_plans', 'action_plans.uuid', '=', 'actions.action_plan_uuid')
            ->whereIn('actions.structure_uuid', $structures)
            ->where('action_plans.status', true)
            ->sum('action_fund_receipts.converted_amount');

        // Group by funding source
        $byFundingSource = DB::table('action_fund_receipts')
            ->select(
                'funding_sources.uuid',
                'funding_sources.name',
                DB::raw('SUM(action_fund_receipts.converted_amount) as total_amount')
            )
            ->join('actions', 'actions.uuid', '=', 'action_fund_receipts.action_uuid')
            ->join('action_plans', 'action_plans.uuid', '=', 'actions.action_plan_uuid')
            ->join('funding_sources', 'funding_sources.uuid', '=', 'action_fund_receipts.funding_source_uuid')
            ->whereIn('actions.structure_uuid', $structures)
            ->where('action_plans.status', true)
            ->groupBy('funding_sources.uuid', 'funding_sources.name')
            ->get()
            ->map(function ($row) use ($total) {
                $row->participation_rate = $total > 0 ? round(($row->total_amount / $total) * 100, 2) : 0;
                return $row;
            });

        // Group by currency
        $byCurrency = DB::table('action_fund_receipts')
            ->select(
                'currencies.uuid',
                'currencies.name',
                'currencies.code',
                DB::raw('SUM(action_fund_receipts.amount_original) as total_amount')
            )
            ->join('actions', 'actions.uuid', '=', 'action_fund_receipts.action_uuid')
            ->join('action_plans', 'action_plans.uuid', '=', 'actions.action_plan_uuid')
            ->join('currencies', 'currencies.uuid', '=', 'action_fund_receipts.currency_uuid')
            ->whereIn('actions.structure_uuid', $structures)
            ->where('action_plans.status', true)
            ->groupBy('currencies.uuid', 'currencies.name', 'currencies.code')
            ->get()
            ->map(function ($row) use ($total) {
                $row->participation_rate = $total > 0 ? round(($row->total_amount / $total) * 100, 2) : 0;
                return $row;
            });

        return [
            'total_acquisitions' => $total,
            'acquisitions_by_funding_source' => $byFundingSource,
            'acquisitions_by_currency' => $byCurrency,
        ];
    }

    /**
     * Expense report grouped by budget type, expense type, and structure.
     */
    public function getExpenseReport(Structure $structure): array
    {
        $structures = [];
        $collect = function ($s) use (&$structures, &$collect) {
            foreach ($s->children as $child) {
                if ($child->status) {
                    $structures[] = $child->uuid;
                }
                $collect($child);
            }
        };
        $collect($structure);

        $actionUuids = DB::table('actions')
            ->join('action_plans', 'action_plans.uuid', '=', 'actions.action_plan_uuid')
            ->whereIn('actions.structure_uuid', $structures)
            ->where('action_plans.status', true)
            ->pluck('actions.uuid');

        $byBudgetType = ActionFundDisbursement::query()
            ->select(
                'budget_types.uuid',
                'budget_types.name',
                DB::raw('SUM(action_fund_disbursements.payment_amount) as total_amount')
            )
            ->join('budget_types', 'budget_types.uuid', '=', 'action_fund_disbursements.budget_type_uuid')
            ->whereIn('action_fund_disbursements.action_uuid', $actionUuids)
            ->groupBy('budget_types.uuid', 'budget_types.name')
            ->get();

        $byExpenseType = DB::table('action_fund_disbursement_expense_types as afdet')
            ->select(
                'et.uuid',
                'et.name',
                DB::raw('SUM(afd.payment_amount) as total_amount')
            )
            ->join('expense_types as et', 'et.uuid', '=', 'afdet.expense_type_uuid')
            ->join('action_fund_disbursements as afd', 'afd.uuid', '=', 'afdet.action_fund_disbursement_uuid')
            ->join('actions as a', 'a.uuid', '=', 'afd.action_uuid')
            ->join('action_plans as ap', 'ap.uuid', '=', 'a.action_plan_uuid')
            ->whereIn('a.structure_uuid', $structures)
            ->where('ap.status', true)
            ->groupBy('et.uuid', 'et.name')
            ->get();

        $byStructure = ActionFundDisbursement::query()
            ->select(
                'structures.uuid',
                'structures.abbreviation',
                'structures.name',
                DB::raw('SUM(action_fund_disbursements.payment_amount) as total_amount')
            )
            ->join('actions', 'actions.uuid', '=', 'action_fund_disbursements.action_uuid')
            ->join('action_plans', 'action_plans.uuid', '=', 'actions.action_plan_uuid')
            ->join('structures', 'structures.uuid', '=', 'actions.structure_uuid')
            ->whereIn('actions.structure_uuid', $structures)
            ->where('action_plans.status', true)
            ->groupBy('structures.uuid', 'structures.abbreviation', 'structures.name')
            ->get();

        return [
            'expenses_by_budget_type' => $byBudgetType,
            'expenses_by_expense_type' => $byExpenseType,
            'expenses_by_structure' => $byStructure,
        ];
    }

    public function getExpensesByObjective(Structure $structure): array
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

        $byObjective = DB::table('strategic_objectives')
            ->select(
                'strategic_objectives.uuid',
                'strategic_objectives.name',
                DB::raw('SUM(action_fund_disbursements.payment_amount) as total_amount')
            )
            ->join('action_objective', 'action_objective.objective_uuid', '=', 'strategic_objectives.uuid')
            ->join('actions', 'actions.uuid', '=', 'action_objective.action_uuid')
            ->join('action_fund_disbursements', 'action_fund_disbursements.action_uuid', '=', 'actions.uuid')
            ->join('action_plans', 'action_plans.uuid', '=', 'actions.action_plan_uuid')
            ->whereIn('actions.structure_uuid', $structures)
            ->where('action_plans.status', true)
            ->groupBy('strategic_objectives.uuid', 'strategic_objectives.name')
            ->get();

        return [
            'expenses_by_objective' => $byObjective->toArray(),
        ];
    }

    public function getExpensesByAxis(Structure $structure): array
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

        $byAxis = DB::table('strategic_axes')
            ->select(
                'strategic_axes.uuid',
                'strategic_axes.name',
                DB::raw('SUM(action_fund_disbursements.payment_amount) as total_amount')
            )
            ->join('strategic_objectives', 'strategic_objectives.axis_uuid', '=', 'strategic_axes.uuid')
            ->join('action_objective', 'action_objective.objective_uuid', '=', 'strategic_objectives.uuid')
            ->join('actions', 'actions.uuid', '=', 'action_objective.action_uuid')
            ->join('action_fund_disbursements', 'action_fund_disbursements.action_uuid', '=', 'actions.uuid')
            ->join('action_plans', 'action_plans.uuid', '=', 'actions.action_plan_uuid')
            ->whereIn('actions.structure_uuid', $structures)
            ->where('action_plans.status', true)
            ->groupBy('strategic_axes.uuid', 'strategic_axes.name')
            ->get();

        return [
            'expenses_by_axis' => $byAxis->toArray(),
        ];
    }

    public function getExpensesByMap(Structure $structure): array
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

        $byMap = DB::table('strategic_maps')
            ->select(
                'strategic_maps.uuid',
                'strategic_maps.name',
                DB::raw('SUM(action_fund_disbursements.payment_amount) as total_amount')
            )
            ->join('strategic_axes', 'strategic_axes.map_uuid', '=', 'strategic_maps.uuid')
            ->join('strategic_objectives', 'strategic_objectives.axis_uuid', '=', 'strategic_axes.uuid')
            ->join('action_objective', 'action_objective.objective_uuid', '=', 'strategic_objectives.uuid')
            ->join('actions', 'actions.uuid', '=', 'action_objective.action_uuid')
            ->join('action_fund_disbursements', 'action_fund_disbursements.action_uuid', '=', 'actions.uuid')
            ->join('action_plans', 'action_plans.uuid', '=', 'actions.action_plan_uuid')
            ->whereIn('actions.structure_uuid', $structures)
            ->where('action_plans.status', true)
            ->groupBy('strategic_maps.uuid', 'strategic_maps.name')
            ->get();

        return [
            'expenses_by_map' => $byMap->toArray(),
        ];
    }
}
