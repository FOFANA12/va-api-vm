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
        Schema::create('activity_funding_sources', function (Blueprint $table) {
            $table->uuid('capability_domain_uuid');
            $table->uuid('funding_source_uuid');
            $table->decimal('planned_budget', 14, 2)->default(0);

            $table->primary(['capability_domain_uuid', 'funding_source_uuid']);
            $table->foreign('capability_domain_uuid')->references('uuid')->on('capability_domains')->onDelete('cascade');
            $table->foreign('funding_source_uuid')->references('uuid')->on('funding_sources')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_funding_sources');
    }
};
