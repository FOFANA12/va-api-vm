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
        Schema::create('action_metrics', function (Blueprint $table) {
            $table->id();
            $table->uuid('action_uuid')->index();
            $table->unsignedInteger('action_id')->index();
            $table->decimal('realization_rate', 5, 2)->default(0);
            $table->decimal('realization_index', 5, 2)->default(0);
            $table->unsignedInteger('aligned_axes_count')->default(0);
            $table->unsignedInteger('aligned_objectives_count')->default(0);
            $table->unsignedInteger('aligned_maps_count')->default(0);
            $table->timestamps();

            $table->foreign('action_uuid')->references('uuid')->on('actions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_metrics');
    }
};
