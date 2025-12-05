<?php

namespace App\Providers;

use App\Models\Action;
use App\Models\ActionControl;
use App\Models\ActionDomain;
use App\Models\ActionFundDisbursement;
use App\Models\CapabilityDomain;
use App\Models\Decision;
use App\Models\DecisionStatus;
use App\Models\ElementaryLevel;
use App\Models\Indicator;
use App\Models\StrategicDomain;
use App\Models\IndicatorControl;
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
            ActionDomain::class,
            StrategicDomain::class,
            CapabilityDomain::class,
            ElementaryLevel::class,
            ActionControl::class,
            Supplier::class,
            ActionFundDisbursement::class,
            IndicatorControl::class
        ]);
    }
}
