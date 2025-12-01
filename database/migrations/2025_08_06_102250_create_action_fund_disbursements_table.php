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
        Schema::create('action_fund_disbursements', function (Blueprint $table) {
             $table->id();
            $table->uuid('uuid')->unique();

            $table->uuid('action_uuid');
            $table->string('reference', 100)->unique()->nullable();
            $table->string('operation_number', 50);
            $table->date('signature_date');
            $table->date('execution_date');
            $table->date('payment_date');
            $table->decimal('payment_amount', 14, 2);

            $table->uuid('payment_mode_uuid');
            $table->string('cheque_reference', 100);
            $table->uuid('budget_type_uuid');
            $table->uuid('phase_uuid')->nullable();
            $table->uuid('task_uuid')->nullable();

            $table->uuid('supplier_uuid');
            $table->uuid('contract_uuid');

            $table->text('description')->nullable();

            $table->timestamps();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();

            $table->foreign('action_uuid')->references('uuid')->on('actions')->onDelete('restrict');
            $table->foreign('supplier_uuid')->references('uuid')->on('suppliers')->onDelete('restrict');
            $table->foreign('contract_uuid')->references('uuid')->on('contracts')->onDelete('restrict');
            $table->foreign('payment_mode_uuid')->references('uuid')->on('payment_modes')->onDelete('restrict');
            $table->foreign('budget_type_uuid')->references('uuid')->on('budget_types')->onDelete('restrict');
            $table->foreign('phase_uuid')->references('uuid')->on('action_phases')->onDelete('restrict');
            $table->foreign('task_uuid')->references('uuid')->on('tasks')->onDelete('restrict');
            $table->foreign('created_by')->references('uuid')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('uuid')->on('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_fund_disbursements');
    }
};
