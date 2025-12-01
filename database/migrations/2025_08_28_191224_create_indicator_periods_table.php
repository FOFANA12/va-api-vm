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
        Schema::create('indicator_periods', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('indicator_uuid');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('target_value', 12, 2);
            $table->decimal('achieved_value', 12, 2)->default(0);

            $table->foreign('indicator_uuid')->references('uuid')->on('indicators')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicator_periods');
    }
};
