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
        Schema::create('strategic_objectives', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('reference', 100)->unique()->nullable();
            $table->string('name', 200);
            $table->uuid('structure_uuid');
            $table->uuid('strategic_map_uuid');
            $table->uuid('strategic_element_uuid');
            $table->uuid('lead_structure_uuid');
            $table->uuid('matrix_period_uuid')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->string('priority', 50);
            $table->string('risk_level', 50);
            $table->string('status', 50)->default('declared');
            $table->string('state', 50)->default('none');
            $table->uuid('status_changed_by')->nullable();
            $table->dateTime('status_changed_at')->nullable();

            $table->boolean('failed')->default(true);
            $table->boolean('alert')->default(false);

            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['strategic_element_uuid', 'name']);

            $table->foreign('structure_uuid')->references('uuid')->on('structures')->onDelete('restrict');
            $table->foreign('strategic_map_uuid')->references('uuid')->on('strategic_maps')->onDelete('restrict');
            $table->foreign('strategic_element_uuid')->references('uuid')->on('strategic_elements')->onDelete('restrict');
            $table->foreign('lead_structure_uuid')->references('uuid')->on('structures')->onDelete('restrict');
            $table->foreign('matrix_period_uuid')->references('uuid')->on('matrix_periods')->onDelete('set null');
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
        Schema::dropIfExists('strategic_objectives');
    }
};
