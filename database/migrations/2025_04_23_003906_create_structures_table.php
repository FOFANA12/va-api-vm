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
        Schema::create('structures', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('abbreviation', 20)->unique();
            $table->string('name', 100)->unique();
            $table->uuid('parent_uuid')->nullable();
            $table->string('type', 50);
            $table->boolean('status')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::table('structures', function (Blueprint $table) {
            $table->foreign('parent_uuid')->references('uuid')->on('structures')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('structures');
    }
};
