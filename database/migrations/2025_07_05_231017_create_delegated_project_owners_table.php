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
        Schema::create('delegated_project_owners', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('project_owner_uuid');
            $table->string('name', 100);
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->boolean('status')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('project_owner_uuid')->references('uuid')->on('project_owners')->onDelete('restrict');
            $table->foreign('created_by')->references('uuid')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('uuid')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delegated_project_owners');
    }
};
