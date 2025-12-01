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
        Schema::create('supplier_evaluations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->uuid('supplier_uuid');
            $table->tinyInteger('score_delay')->unsigned();
            $table->tinyInteger('score_price')->unsigned();
            $table->tinyInteger('score_quality')->unsigned();
            $table->decimal('total_score', 3, 2)->nullable()->default(0);
            $table->text('comment')->nullable();
            $table->date('evaluated_at')->nullable();
            $table->uuid('evaluated_by')->nullable();

            $table->timestamps();

            $table->foreign('supplier_uuid')->references('uuid')->on('suppliers')->onDelete('cascade');
            $table->foreign('evaluated_by')->references('uuid')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_evaluations');
    }
};
