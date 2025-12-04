<?php

use App\Http\Controllers\ActionAlignmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\DecisionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\ActionPlanController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ActionPhaseController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\StrategicMapController;
use App\Http\Controllers\ActionControlController;
use App\Http\Controllers\ActionDomainController;
use App\Http\Controllers\ActionDomainStateController;
use App\Http\Controllers\ActionDomainStatusController;
use App\Http\Controllers\Settings\UserController;
use App\Http\Controllers\ActionPlanningController;
use App\Http\Controllers\DecisionStatusController;
use App\Http\Controllers\Settings\RegionController;
use App\Http\Controllers\IndicatorControlController;
use App\Http\Controllers\ActionFundReceiptController;
use App\Http\Controllers\IndicatorPlanningController;
use App\Http\Controllers\MatrixPeriodController;
use App\Http\Controllers\Settings\CurrencyController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\StrategicObjectiveController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Settings\BudgetTypeController;
use App\Http\Controllers\Settings\DepartmentController;
use App\Http\Controllers\Settings\BeneficiaryController;
use App\Http\Controllers\Settings\ExpenseTypeController;
use App\Http\Controllers\Settings\StakeholderController;
use App\Http\Controllers\StrategicStakeholderController;
use App\Http\Controllers\Settings\MunicipalityController;
use App\Http\Controllers\Settings\PaymentModelController;
use App\Http\Controllers\Settings\ProjectOwnerController;
use App\Http\Controllers\ActionFundDisbursementController;
use App\Http\Controllers\ActionStatusController;
use App\Http\Controllers\CapabilityDomainController;
use App\Http\Controllers\CapabilityDomainStateController;
use App\Http\Controllers\CapabilityDomainStatusController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\IndicatorStatusController;
use App\Http\Controllers\Report\ActionPerformanceReportController;
use App\Http\Controllers\LogActivityController;
use App\Http\Controllers\Report\DahsboardReportController;
use App\Http\Controllers\Report\IndicatorPerformanceReportController;
use App\Http\Controllers\Report\StructureReportController;
use App\Http\Controllers\Report\StructureStatisticReportController;
use App\Http\Controllers\Settings\DefaultPhaseController;
use App\Http\Controllers\Settings\FundingSourceController;
use App\Http\Controllers\Settings\IndicatorCategoryController;
use App\Http\Controllers\Settings\DelegatedProjectOwnerController;
use App\Http\Controllers\Settings\FileTypeController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\StrategicDomainController;
use App\Http\Controllers\StrategicDomainStateController;
use App\Http\Controllers\StrategicDomainStatusController;
use App\Http\Controllers\StrategicElementController;
use App\Http\Controllers\StrategicObjectiveAlignmentController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierEvaluationController;
use App\Http\Controllers\TaskController;

// ==========================
// AUTHENTICATION ROUTES
// ==========================
Route::prefix('api-auth')->controller(LoginController::class)->group(function () {
    Route::post('login', 'apiLogin')->middleware('throttle:5,1');
    Route::post('logout', 'apiLogout')->middleware('auth:sanctum');
});

Route::prefix('spa-auth')->controller(LoginController::class)->group(function () {
    Route::post('login', 'spaLogin')->middleware('throttle:5,1');
    Route::post('logout', 'spaLogout')->middleware('auth:sanctum');
});

// Profile routes
Route::middleware('auth:sanctum')->controller(ProfileController::class)->prefix('profile')->group(function () {
    Route::get('/', 'getProfile');
    Route::put('/', 'update');
});

// Password Reset
Route::prefix('password')->group(function () {
    Route::post('forgot', [ForgotPasswordController::class, 'forgetPassword'])->middleware('throttle:3,1');
    Route::post('reset', [ResetPasswordController::class, 'resetPassword'])->middleware('throttle:5,1');
});


// ==========================
// SETTINGS ROUTES
// ==========================
Route::prefix('settings')->group(function () {
    Route::prefix('currencies')->group(function () {
        Route::post('destroy', [CurrencyController::class, 'destroy']);
        Route::get('default', [CurrencyController::class, 'getDefaultCurrency']);
    });
    Route::apiResource('currencies', CurrencyController::class)->only(['index', 'store', 'show', 'update']);

    Route::prefix('funding-sources')->controller(FundingSourceController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('funding-sources', FundingSourceController::class)->only(['index', 'store', 'show', 'update']);

    Route::prefix('regions')->controller(RegionController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('regions', RegionController::class)->only(['index', 'store', 'show', 'update']);

    Route::prefix('departments')->controller(DepartmentController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('departments', DepartmentController::class)->only(['index', 'store', 'show', 'update']);

    Route::prefix('municipalities')->controller(MunicipalityController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('municipalities', MunicipalityController::class)->only(['index', 'store', 'show', 'update']);

    Route::prefix('project-owners')->controller(ProjectOwnerController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('project-owners', ProjectOwnerController::class)->only(['index', 'store', 'show', 'update'])->parameters(["project-owners" => "projectOwner"]);

    Route::prefix('delegated-project-owners')->controller(DelegatedProjectOwnerController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('delegated-project-owners', DelegatedProjectOwnerController::class)->only(['index', 'store', 'show', 'update'])->parameters(["delegated-project-owners" => "delegatedProjectOwner"]);

    Route::prefix('beneficiaries')->controller(BeneficiaryController::class)->group(function () {
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('beneficiaries', BeneficiaryController::class)->only(['index', 'store', 'show', 'update']);

    Route::prefix('stakeholders')->controller(StakeholderController::class)->group(function () {
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('stakeholders', StakeholderController::class)->only(['index', 'store', 'show', 'update']);

    Route::prefix('payment-modes')->controller(PaymentModelController::class)->group(function () {
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('payment-modes', PaymentModelController::class)->only(['index', 'store', 'show', 'update']);

    Route::prefix('expense-types')->controller(ExpenseTypeController::class)->group(function () {
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('expense-types', ExpenseTypeController::class)->only(['index', 'store', 'show', 'update']);

    Route::prefix('budget-types')->controller(BudgetTypeController::class)->group(function () {
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('budget-types', BudgetTypeController::class)->only(['index', 'store', 'show', 'update']);

    Route::prefix('indicator-categories')->controller(IndicatorCategoryController::class)->group(function () {
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('indicator-categories', IndicatorCategoryController::class)->only(['index', 'store', 'show', 'update']);

    Route::prefix('default-phases')->controller(DefaultPhaseController::class)->group(function () {
        Route::get('requirements', 'requirements');
    });
    Route::apiResource('default-phases', DefaultPhaseController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::prefix('file-types')->controller(FileTypeController::class)->group(function () {
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('file-types', FileTypeController::class)->only(['index', 'store', 'show', 'update']);

    Route::prefix('users')->controller(UserController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('users', UserController::class)->only(['index', 'store', 'show', 'update']);

    // Roles
    Route::prefix('roles')->group(function () {
        Route::get('requirements', [RoleController::class, 'requirements']);
        Route::post('destroy', [RoleController::class, 'destroy']);
    });
    Route::apiResource('roles', RoleController::class)->only(['index', 'store', 'show', 'update']);
});


// ==========================
// MAIN AUTHENTICATED ROUTES
// ==========================
Route::middleware('auth:sanctum')->group(function () {

    // Structures
    Route::prefix('structures')->controller(StructureController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('structures', StructureController::class)->only(['index', 'store', 'show', 'update']);

    // Action domains
    Route::prefix('action-domains')->controller(ActionDomainController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('action-domains', ActionDomainController::class)->only(['index', 'store', 'show', 'update']);

    //Program statuses
    Route::prefix('action-domain-statuses/{action_domain}')->controller(ActionDomainStatusController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/requirements', 'requirements');
        Route::post('/', 'store');
        Route::post('/destroy', 'destroy');
    });

    //Program states
    Route::prefix('action-domain-states/{action_domain}')->controller(ActionDomainStateController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/requirements', 'requirements');
        Route::post('/', 'store');
        Route::post('/destroy', 'destroy');
    });

    // strategic domains
    Route::prefix('strategic-domains')->controller(StrategicDomainController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('strategic-domains', StrategicDomainController::class)->only(['index', 'store', 'show', 'update']);

    //Strategic domain statuses
    Route::prefix('strategic-domain-statuses/{strategic_domain}')->controller(StrategicDomainStatusController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/requirements', 'requirements');
        Route::post('/', 'store');
        Route::post('/destroy', 'destroy');
    });

    //Strategic domain states
    Route::prefix('strategic-domain-states/{strategic_domain}')->controller(StrategicDomainStateController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/requirements', 'requirements');
        Route::post('/', 'store');
        Route::post('/destroy', 'destroy');
    });

    // capability domains
    Route::prefix('capability-domains')->controller(CapabilityDomainController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('capability-domains', CapabilityDomainController::class)->only(['index', 'store', 'show', 'update']);

    //Capability domains statuses
    Route::prefix('capability-domain-statuses/{capability_domain}')->controller(CapabilityDomainStatusController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/requirements', 'requirements');
        Route::post('/', 'store');
        Route::post('/destroy', 'destroy');
    });

    //Capability domains states
    Route::prefix('capability-domain-states/{capability_domain}')->controller(CapabilityDomainStateController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/requirements', 'requirements');
        Route::post('/', 'store');
        Route::post('/destroy', 'destroy');
    });

    //Attachments
    Route::prefix('attachments')->controller(AttachmentController::class)->group(function () {
        Route::get('/requirements', 'requirements');
        Route::post('/destroy', 'destroy');
    });
    Route::apiResource('attachments', AttachmentController::class)->only(['index', 'store', 'show']);

    // Log activies
    Route::prefix('log-activities')->controller(LogActivityController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('{activity}', 'show');
        Route::post('/destroy', 'destroy');
    });

    // Employees
    Route::prefix('employees')->controller(EmployeeController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('employees', EmployeeController::class)->only(['index', 'store', 'show', 'update']);

    // Supplier
    Route::prefix('suppliers')->controller(SupplierController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('suppliers', SupplierController::class)->only(['index', 'store', 'show', 'update']);

    // Contract
    Route::prefix('supplier-contracts')->controller(ContractController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('{contract}', 'show');
        Route::put('{contract}', 'update');
        Route::post('destroy', 'destroy');
        Route::post('{supplier}', 'store');
    });

    // Supplier evaluation
    Route::prefix('supplier-evaluations')->controller(SupplierEvaluationController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('{supplier_evaluation}', 'show');
        Route::put('{supplier_evaluation}', 'update');
        Route::post('{supplier}/destroy', 'destroy');
        Route::post('{supplier}', 'store');
    });

    // Strategic map
    Route::prefix('strategic-maps')->controller(StrategicMapController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('strategic-maps', StrategicMapController::class)->only(['index', 'store', 'show', 'update']);

    // Strategic stakeholders
    Route::prefix('strategic-stakeholders')->controller(StrategicStakeholderController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('{strategic_stakeholder}', 'show');
        Route::put('{strategic_stakeholder}', 'update');
        Route::post('destroy', 'destroy');
        Route::post('{strategic_map}', 'store');
    });

    // Matrix periods
    Route::prefix('matrix-periods')->controller(MatrixPeriodController::class)->group(function () {
        Route::get('map/{strategic_map}', 'getMatrixPeriods');
        Route::get('map/{strategic_map}/{matrix_period}/objectives/available', 'availableObjectives');
        Route::post('map/{strategic_map}', 'store');
        Route::get('{matrix_period}', 'show');
        Route::put('{matrix_period}', 'update');
        Route::post('{matrix_period}/objectives', 'attachObjectives');
        Route::delete('{matrix_period}/objectives/{objective}', 'detachObjective');
        Route::delete('destroy/{matrix_period}', 'destroy');
    });

    // Strategic element
    Route::prefix('strategic-elements')->controller(StrategicElementController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('strategic-elements', StrategicElementController::class)->only(['index', 'store', 'show', 'update']);

    // Strategic objective
    Route::prefix('strategic-objectives')->controller(StrategicObjectiveController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::get('{strategic_objective}/statuses', 'getStatuses');
        Route::put('{strategic_objective}/status', 'updateStatus');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('strategic-objectives', StrategicObjectiveController::class)->only(['index', 'store', 'show', 'update']);

    // Strategic objective Alignments
    Route::prefix('objective-alignments')->controller(StrategicObjectiveAlignmentController::class)->group(function () {
        Route::get('structures', 'getStructures');
        Route::get('actions/{structure}/{strategic_objective}', 'getActions');
        Route::get('/', 'index');
        Route::post('align/{strategic_objective}', 'align');
        Route::post('unalign', 'unalign');
    });

    // Action Plans
    Route::prefix('action-plans')->controller(ActionPlanController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('duplicate', 'duplicate');
        Route::post('destroy', 'destroy');
        Route::post('import', 'import');
    });
    Route::apiResource('action-plans', ActionPlanController::class)->only(['index', 'store', 'show', 'update']);

    // Decisions
    Route::prefix('decisions')->controller(DecisionController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('decisions', DecisionController::class)->only(['index', 'store', 'show', 'update']);

    // Decision Statuses
    Route::prefix('decision-statuses')->controller(DecisionStatusController::class)->group(function () {
        Route::get('requirements/{decision}', 'requirements');
        Route::get('/', 'index');
        Route::post('store/{decision}', 'store');
        Route::get('{decision_status}', 'show');
        Route::post('/destroy', 'destroy');
    });

    // Actions
    Route::prefix('actions')->controller(ActionController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('actions', ActionController::class)->only(['index', 'store', 'show', 'update']);

    //Action statuses
    Route::prefix('action-statuses/{action}')->controller(ActionStatusController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/requirements', 'requirements');
        Route::post('/', 'store');
        Route::post('/destroy', 'destroy');
    });

    // Reporting
    Route::prefix('report-actions')->controller(ActionPerformanceReportController::class)->group(function () {
        Route::get('{action}/performance', 'report');
    });

    // Action Planning
    Route::prefix('action-plannings')->controller(ActionPlanningController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::get('{action}', 'show');
        Route::put('{action}', 'update');
    });

    // Action Phases
    Route::prefix('action-phases')->controller(ActionPhaseController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('initialize/{action}', 'initializeDefaultPhases');
        Route::get('{action}', 'index');
        Route::post('{action}', 'store');
        Route::get('phase/{action_phase}', 'show');
        Route::put('phase/{action}/{action_phase}', 'update');
        Route::delete('destroy/{action_phase}', 'destroy');
    });

    // Action Controls
    Route::prefix('action-controls')->controller(ActionControlController::class)->group(function () {
        Route::get('requirements/{action_period}', 'requirements');
        Route::get('/', 'index');
        Route::post('/{action_period}', 'store');
        Route::get('{action_control}', 'show');
        Route::delete('{action_control}', 'destroy');
    });

    // Action Alignments
    Route::prefix('action-alignments')->controller(ActionAlignmentController::class)->group(function () {
        Route::get('requirements/{action}', 'requirements');
        Route::get('/', 'index');
        Route::post('/align/{action}', 'align');
        Route::post('/unalign', 'unalign');
    });


    //Action fund receipt
    Route::prefix('action-fund-receipts')->controller(ActionFundReceiptController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('action-fund-receipts', ActionFundReceiptController::class)->only(['index', 'store', 'show', 'update']);

    // Action fund disbursements
    Route::prefix('action-fund-disbursements')->controller(ActionFundDisbursementController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('action-fund-disbursements', ActionFundDisbursementController::class)->only(['index', 'store', 'show', 'update']);

    // Indicators
    Route::prefix('indicators')->controller(IndicatorController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::get('statuses', 'getStatuses');
        Route::put('{indicator}/status', 'updateStatus');
        Route::post('destroy', 'destroy');
    });
    Route::apiResource('indicators', IndicatorController::class)->only(['index', 'store', 'show', 'update']);

    //Indicator statuses
    Route::prefix('indicator-statuses/{indicator}')->controller(IndicatorStatusController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/requirements', 'requirements');
        Route::post('/', 'store');
        Route::post('/destroy', 'destroy');
    });


    // Reporting
    Route::prefix('report-indicators')->controller(IndicatorPerformanceReportController::class)->group(function () {
        Route::get('{indicator}/performance', 'report');
    });

    // Indicator Planning
    Route::prefix('indicator-plannings')->controller(IndicatorPlanningController::class)->group(function () {
        Route::get('requirements', 'requirements');
        Route::get('{indicator}', 'show');
        Route::put('{indicator}', 'update');
    });

    // Indicator Controls
    Route::prefix('indicator-controls')->controller(IndicatorControlController::class)->group(function () {
        Route::get('requirements/{indicator_period}', 'requirements');
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('{indicator_control}', 'show');
        Route::delete('{indicator_control}', 'destroy');
    });

    // Task
    Route::prefix('tasks')->controller(TaskController::class)->group(function () {
        Route::get('requirements/{action}', 'requirements');
        Route::post('{action_phase}', 'store');
        Route::get('task/{task}', 'show');
        Route::put('task/{task}', 'update');
        Route::patch('task/{task}/toggle', 'toggle');
        Route::delete('destroy/{task}', 'destroy');
    });

    // Structure Reporting
    Route::prefix('report-structure')->group(function () {
        Route::controller(StructureReportController::class)->group(function () {
            Route::get('requirements', 'requirements');
            Route::get('{structure}/performance', 'performance');
        });

        Route::prefix('{structure}/statistics')->controller(StructureStatisticReportController::class)->group(function () {
            Route::get('acquisitions', 'acquisitions');
            Route::get('expenses', 'expenses');
            Route::get('expenses/objectives', 'expensesByObjective');
            Route::get('expenses/axes', 'expensesByAxis');
            Route::get('expenses/maps', 'expensesByMap');
        });
    });

    // Dashboard Reporting
    Route::prefix('report-dashboard')->controller(DahsboardReportController::class)->group(function () {
        Route::get('{structure?}/general', 'general');
    });
});
