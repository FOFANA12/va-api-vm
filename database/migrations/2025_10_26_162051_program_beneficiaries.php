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
        Schema::create('program_beneficiaries', function (Blueprint $table) {
            $table->uuid('program_uuid');
            $table->uuid('beneficiary_uuid');

            $table->primary(['program_uuid', 'beneficiary_uuid']);
            $table->foreign('program_uuid')->references('uuid')->on('programs')->onDelete('cascade');
            $table->foreign('beneficiary_uuid')->references('uuid')->on('beneficiaries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_beneficiaries');
    }
};
