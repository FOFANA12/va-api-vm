<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->call('cache:clear');
        $this->command->call('config:cache');

        $this->command->info('Exécution de migration');

        $this->command->call('migrate:fresh');
        $this->command->warn('Données effacées.');

        $this->call(
            [
                StructureSeeder::class,
                RoleSeeder::class,
                UserSeeder::class,
                CurrencySeeder::class,
                FoundingSourceSeeder::class,
                ActionPlanSeeder::class,
                RegionSeeder::class,
                DepartmentSeeder::class,
                ContractTypeSeeder::class,
                ProcurementModeSeeder::class,
                MunicipalitySeeder::class,
                ActionDomainSeeder::class,
                StrategicDomainSeeder::class,
                ProjectOwnerSeeder::class,
                DelegatedProjectOwnerSeeder::class,
                CapabilityDomainSeeder::class,
                BeneficiarySeeder::class,
                StakeholderSeeder::class,
                ActionSeeder::class,
                PaymentModelSeeder::class,
                ExpenseTypeSeeder::class,
                BudgetTypeSeeder::class,
                SupplierSeeder::class,
                ContractSeeder::class,
                SupplierEvaluationSeeder::class,
                ActionFundDisbursementSeeder::class,
                ActionFundReceiptSeeder::class,
                ActionSeeder::class,
                StrategicMapSeeder::class,
                StrategicElementSeeder::class,
                StrategicObjectiveSeeder::class,
                DecisionSeeder::class,
                DecisionStatusSeeder::class,
                IndicatorCategorySeeder::class,
                MatrixPeriodSeeder::class,
                IndicatorSeeder::class,
                TaskSeeder::class,
                DefaultPhaseSeeder::class,
                FileTypeSeeder::class,
                ElementaryLevelSeeder::class,
            ]
        );
    }
}
