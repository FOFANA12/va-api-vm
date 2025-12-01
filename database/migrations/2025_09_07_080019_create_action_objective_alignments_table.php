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
        Schema::create('action_objective_alignments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('identifier', 255)->unique()->index();
            $table->uuid('action_structure_uuid');
            $table->uuid('action_uuid');

            $table->uuid('objective_structure_uuid');
            $table->uuid('strategic_map_uuid');
            $table->uuid('strategic_element_uuid');
            $table->uuid('objective_uuid');

            $table->uuid('aligned_by')->nullable();
            $table->timestamp('aligned_at')->nullable();

            $table->unique(['action_uuid', 'objective_uuid'], 'uq_action_objective');
            $table->index(['action_uuid', 'objective_uuid'], 'idx_action_objective');
            $table->index(['action_structure_uuid', 'objective_structure_uuid'], 'idx_structures');

            $table->foreign('action_structure_uuid')->references('uuid')->on('structures')->onDelete('cascade');
            $table->foreign('objective_structure_uuid')->references('uuid')->on('structures')->onDelete('cascade');
            $table->foreign('action_uuid')->references('uuid')->on('actions')->onDelete('cascade');
            $table->foreign('strategic_map_uuid')->references('uuid')->on('strategic_maps')->onDelete('cascade');
            $table->foreign('strategic_element_uuid')->references('uuid')->on('strategic_elements')->onDelete('cascade');
            $table->foreign('objective_uuid')->references('uuid')->on('strategic_objectives')->onDelete('cascade');
            $table->foreign('aligned_by')->references('uuid')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_objective_alignments');
    }
};
