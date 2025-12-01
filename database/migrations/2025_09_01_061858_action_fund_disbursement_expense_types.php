<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('action_fund_disbursement_expense_types', function (Blueprint $table) {
            $table->uuid('action_fund_disbursement_uuid');
            $table->uuid('expense_type_uuid');
            $table->decimal('total', 24, 2)->default(0);

            $table->primary(
                ['action_fund_disbursement_uuid', 'expense_type_uuid'],
                'afdet_pk'
            );

            $table->foreign('action_fund_disbursement_uuid', 'afdet_disbursement_fk')
                ->references('uuid')->on('action_fund_disbursements')
                ->onDelete('cascade');

            $table->foreign('expense_type_uuid', 'afdet_expense_type_fk')
                ->references('uuid')->on('expense_types')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('action_fund_disbursement_expense_types', function (Blueprint $table) {
            $table->dropForeign('afdet_disbursement_fk');
            $table->dropForeign('afdet_expense_type_fk');
        });

        Schema::dropIfExists('action_fund_disbursement_expense_types');
    }
};
