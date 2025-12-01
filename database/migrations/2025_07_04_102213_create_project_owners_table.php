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
        Schema::create('project_owners', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('structure_uuid')->nullable();
            $table->string('name', 100)->unique();
            $table->string('type', 50)->nullable();
            $table->string('email', 150)->nullable()->unique();
            $table->string('phone', 30)->nullable()->unique();
            $table->boolean('status')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('structure_uuid')->references('uuid')->on('structures')->onDelete('restrict');
            $table->foreign('created_by')->references('uuid')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('uuid')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_owners');
    }
};
