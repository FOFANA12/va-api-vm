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
        Schema::create('structure_metrics', function (Blueprint $table) {
            $table->id();
            $table->uuid('structure_uuid')->index();
            $table->unsignedInteger('structure_id')->index();
            $table->unsignedInteger('aligned_axes_count')->default(0);
            $table->unsignedInteger('aligned_objectives_count')->default(0);
            $table->unsignedInteger('aligned_maps_count')->default(0);
            $table->timestamps();

            $table->foreign('structure_uuid')->references('uuid')->on('structures')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('structure_metrics');
    }
};
