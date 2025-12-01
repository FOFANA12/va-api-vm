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
        Schema::create('action_funding_sources', function (Blueprint $table) {
            $table->uuid('action_uuid');
            $table->uuid('funding_source_uuid');
            $table->decimal('planned_budget', 14, 2)->default(0);

            $table->primary(['action_uuid', 'funding_source_uuid']);
            $table->foreign('action_uuid')->references('uuid')->on('actions')->onDelete('cascade');
            $table->foreign('funding_source_uuid')->references('uuid')->on('funding_sources')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_funding_sources');
    }
};
