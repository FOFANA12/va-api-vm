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
        Schema::create('project_funding_sources', function (Blueprint $table) {
            $table->uuid('project_uuid');
            $table->uuid('funding_source_uuid');
            $table->decimal('planned_budget', 14, 2)->default(0);

            $table->primary(['project_uuid', 'funding_source_uuid']);
            $table->foreign('project_uuid')->references('uuid')->on('projects')->onDelete('cascade');
            $table->foreign('funding_source_uuid')->references('uuid')->on('funding_sources')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_funding_sources');
    }
};
