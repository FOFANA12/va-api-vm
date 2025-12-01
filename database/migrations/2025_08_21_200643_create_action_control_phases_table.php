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
        Schema::create('action_control_phases', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('action_control_uuid');
            $table->uuid('phase_uuid');
            $table->decimal('progress_percent', 5, 2);
            $table->decimal('weight');

            $table->foreign('action_control_uuid')->references('uuid')->on('action_controls')->onDelete('cascade');
            $table->foreign('phase_uuid')->references('uuid')->on('action_phases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_control_phases');
    }
};
