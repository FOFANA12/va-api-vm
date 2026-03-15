<?php

namespace App\Helpers;

use App\Models\Action;
use App\Models\ActionFundDisbursement;

class ReferenceGenerator
{
    /**
     * Generate a unique reference for an Action Plan.
     */
    public static function generateActionPlanReference(int $id): string
    {
        $now = now();
        $year = $now->year;
        $month = str_pad($now->month, 2, '0', STR_PAD_LEFT);
        $day = str_pad($now->day, 2, '0', STR_PAD_LEFT);

        return "PA-{$year}-{$month}-{$day}-" . str_pad((string) $id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique reference for Action.
     */
    public static function generateActionReference(int $id, $abbStructure): string
    {

        return $abbStructure . '_ACT' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique reference for Action Domain.
     */
    public static function generateActionDomainReference(int $id): string
    {

        return "DOM_ACT-" . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique reference for Strategic Domain.
     */
    public static function generateStrategicDomainReference(int $id): string
    {

        return "DOM_STG-" . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }
    /**
     * Generate a unique reference for Capability Domain.
     */
    public static function generateCapabilityDomainReference(int $id): string
    {

        return "DOM_CAP-" . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique reference for elementary level.
     */
    public static function generateElementaryLevelReference(int $id): string
    {

        return "NIV_ELE-" . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique reference for an Action Plan.
     */
    public static function generateActionFundDisbursementReference(ActionFundDisbursement $actionFundDisbursement, Action $action): string
    {
        if ($actionFundDisbursement->phase) {
            return 'DEC-' . $action->structure->abbreviation . '-' . $action->reference . '-' . $actionFundDisbursement->phase->number . '-' . str_pad((string) $actionFundDisbursement->id, 3, '0', STR_PAD_LEFT);
        }

        return 'DEC-' . $action->structure->abbreviation . '-' . $action->reference . '-' . str_pad((string) $actionFundDisbursement->id, 3, '0', STR_PAD_LEFT);
    }

    public static function generateFundReceiptReference(int $id, Action $action): string
    {
        return 'ENC-' . $action->structure->abbreviation . '-' . $action->reference . '-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }


    /**
     * Generate a unique reference for strategic objective.
     */
    public static function generateStrategicObjectiveReference(int $id, string $abbStructure, string $abbAxe): string
    {

        return $abbStructure . '_' . $abbAxe . '_OBJ' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }


    public static function generateDecisionReference(
        int $decisionId,
        string $decidableReference,
    ): string {
        return $decidableReference . '_DECIS' . str_pad((string) $decisionId, 3, '0', STR_PAD_LEFT);
    }




    /**
     * Generate a unique reference for indicator.
     */
    public static function generateIndicatorReference(int $id,  string $objectiveReference): string
    {

        return $objectiveReference . "_IND" . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique reference for strategic element.
     */
    public static function generateStrategicElementReference(
        int $id,
        string $type,
        string $abbStructure,
    ): string {

        $prefix = $type === 'LEVER' ? 'LEV' : 'AX';

        return $abbStructure . '_' . $prefix .
            str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }
}
