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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('phase_uuid')->nullable();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('priority', 50);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_completed')->default(false);
            $table->uuid('assigned_to')->nullable();
            $table->text('deliverable')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();

            $table->timestamps();

            $table->foreign('phase_uuid')->references('uuid')->on('action_phases')->onDelete('set null');
            $table->foreign('assigned_to')->references('uuid')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('uuid')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('uuid')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
