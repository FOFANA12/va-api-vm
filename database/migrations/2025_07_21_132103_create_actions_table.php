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
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('reference', 100)->nullable()->unique();
            $table->uuid('structure_uuid');
            $table->uuid('action_plan_uuid');
            $table->uuid('project_owner_uuid');
            $table->uuid('delegated_project_owner_uuid');
            $table->uuid('region_uuid')->nullable();
            $table->uuid('department_uuid')->nullable();
            $table->uuid('municipality_uuid')->nullable();
            $table->uuid('program_uuid')->nullable();
            $table->uuid('project_uuid')->nullable();
            $table->uuid('activity_uuid')->nullable();
            $table->string('name', 100);
            $table->string('priority', 50);
            $table->string('risk_level', 50);
            $table->text('description')->nullable();
            $table->text('prerequisites')->nullable();
            $table->text('impacts')->nullable();
            $table->text('risks')->nullable();
            $table->string('generate_document_type', 20);
            $table->string('state', 50)->default('none');
            $table->string('status', 50)->default('created');
            $table->timestamp('status_changed_at')->nullable();
            $table->uuid('status_changed_by')->nullable();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();

            $table->decimal('total_budget', 24, 2)->default(0);
            $table->decimal('total_receipt_fund', 24, 2)->default(0);
            $table->decimal('total_disbursement_fund', 24, 2)->default(0);

            $table->string('currency', 10)->default('MRU');
            $table->string('frequency_unit', 20)->nullable();
            $table->tinyInteger('frequency_value')->nullable();
            $table->boolean('is_planned')->default(false);
            $table->decimal('actual_progress_percent', 5, 2)->default(0);

            $table->boolean('failed')->default(false); // vain
            $table->boolean('alert')->default(false);

            $table->uuid('updated_by')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('structure_uuid')->references('uuid')->on('structures')->onDelete('restrict');
            $table->foreign('action_plan_uuid')->references('uuid')->on('action_plans')->onDelete('restrict');
            $table->foreign('project_owner_uuid')->references('uuid')->on('project_owners')->onDelete('restrict');
            $table->foreign('delegated_project_owner_uuid')->references('uuid')->on('delegated_project_owners')->onDelete('restrict');
            $table->foreign('program_uuid')->references('uuid')->on('programs')->onDelete('restrict');
            $table->foreign('project_uuid')->references('uuid')->on('projects')->onDelete('restrict');
            $table->foreign('activity_uuid')->references('uuid')->on('activities')->onDelete('restrict');
            $table->foreign('region_uuid')->references('uuid')->on('regions')->onDelete('restrict');
            $table->foreign('department_uuid')->references('uuid')->on('departments')->onDelete('restrict');
            $table->foreign('municipality_uuid')->references('uuid')->on('municipalities')->onDelete('restrict');
            $table->foreign('created_by')->references('uuid')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('uuid')->on('users')->onDelete('set null');
            $table->foreign('status_changed_by')->references('uuid')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actions');
    }
};
