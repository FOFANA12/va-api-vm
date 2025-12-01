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
        Schema::create('default_phases', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 100)->unique();
            $table->tinyInteger('number')->default(0)->unsigned();
            $table->integer('duration')->unsigned();
            $table->decimal('weight');
            $table->text('description')->nullable();
            $table->text('deliverable')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('uuid')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('uuid')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_phases');
    }
};
