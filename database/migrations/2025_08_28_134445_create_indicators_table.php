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
        Schema::create('indicators', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('reference', 100)->nullable()->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->uuid('structure_uuid');
            $table->uuid('strategic_map_uuid');
            $table->uuid('strategic_element_uuid');
            $table->uuid('strategic_objective_uuid');
            $table->uuid('lead_structure_uuid');
            $table->uuid('category_uuid');
            $table->string('chart_type', 20);
            $table->string('frequency_unit', 20)->nullable();
            $table->integer('frequency_value')->nullable();
            $table->decimal('initial_value', 12, 2)->default(0);
            $table->decimal('final_target_value', 12, 2);
            $table->decimal('achieved_value', 12, 2)->default(0);
            $table->string('unit', 20);
            $table->string('status', 20)->default('created');
            $table->string('state', 50)->default('none');
            $table->uuid('status_changed_by')->nullable();
            $table->timestamp('status_changed_at')->nullable();
            $table->boolean('is_planned')->default(false);

            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();

            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('structure_uuid')->references('uuid')->on('structures')->onDelete('restrict');
            $table->foreign('strategic_map_uuid')->references('uuid')->on('strategic_maps')->onDelete('restrict');
            $table->foreign('strategic_element_uuid')->references('uuid')->on('strategic_elements')->onDelete('restrict');
            $table->foreign('strategic_objective_uuid')->references('uuid')->on('strategic_objectives')->onDelete('restrict');
            $table->foreign('lead_structure_uuid')->references('uuid')->on('structures')->onDelete('restrict');
            $table->foreign('category_uuid')->references('uuid')->on('indicator_categories')->onDelete('restrict');
            $table->foreign('status_changed_by')->references('uuid')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('uuid')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('uuid')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicators');
    }
};
