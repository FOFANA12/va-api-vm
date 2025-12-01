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
        Schema::create('decisions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('reference', 100)->nullable()->unique();
            $table->date('decision_date');
            $table->morphs('decidable');
            $table->string('title', 150);
            $table->string('priority', 50);
            $table->text('description');
            $table->string('status', 50)->default('announced');
            $table->timestamp('status_changed_at')->nullable();
            $table->uuid('status_changed_by')->nullable();
            $table->timestamps();

            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();

            $table->foreign('created_by')->references('uuid')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('uuid')->on('users')->onDelete('set null');
            $table->foreign('status_changed_by')->references('uuid')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decisions');
    }
};
