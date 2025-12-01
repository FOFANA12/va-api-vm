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
        Schema::create('activity_beneficiaries', function (Blueprint $table) {
            $table->uuid('activity_uuid');
            $table->uuid('beneficiary_uuid');

            $table->primary(['activity_uuid', 'beneficiary_uuid']);
            $table->foreign('activity_uuid')->references('uuid')->on('activities')->onDelete('cascade');
            $table->foreign('beneficiary_uuid')->references('uuid')->on('beneficiaries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_beneficiaries');
    }
};
