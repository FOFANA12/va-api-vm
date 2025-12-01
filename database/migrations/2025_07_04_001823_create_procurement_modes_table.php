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
        Schema::create('procurement_modes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('contract_type_uuid');
            $table->string('name', 50)->unique();
            $table->integer('duration')->default(0);
            $table->boolean('status')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();


            $table->foreign('contract_type_uuid')->references('uuid')->on('contract_types')->onDelete('restrict');
            $table->foreign('created_by')->references('uuid')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('uuid')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_modes');
    }
};
