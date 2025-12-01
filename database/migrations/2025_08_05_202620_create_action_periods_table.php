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
        Schema::create('action_periods', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('action_uuid');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('progress_percent', 5, 2)->default(0);
            $table->decimal('actual_progress_percent', 5, 2)->default(0);

            $table->foreign('action_uuid')->references('uuid')->on('actions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_periods');
    }
};
