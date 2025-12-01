<?php

namespace App\Providers;

use App\Models\Action;
use App\Models\ActionControl;
use App\Models\ActionFundDisbursement;
use App\Models\Activity;
use App\Models\Decision;
use App\Models\DecisionStatus;
use App\Models\Indicator;
use App\Models\Program;
use App\Models\Project;
use App\Models\StrategicObjective;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            User::class,
            Action::class,
            StrategicObjective::class,
            Indicator::class,
            Decision::class,
            DecisionStatus::class,
            Program::class,
            Project::class,
            Activity::class,
            ActionControl::class,
            Supplier::class,
            ActionFundDisbursement::class
        ]);
    }
}
