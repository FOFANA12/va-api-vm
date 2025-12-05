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
        Schema::create('elementary_level_beneficiaries', function (Blueprint $table) {
            $table->uuid('elementary_level_uuid');
            $table->uuid('beneficiary_uuid');

            $table->primary(['elementary_level_uuid', 'beneficiary_uuid']);
            $table->foreign('elementary_level_uuid')->references('uuid')->on('elementary_levels')->onDelete('cascade');
            $table->foreign('beneficiary_uuid')->references('uuid')->on('beneficiaries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elementary_level_beneficiaries');
    }
};
