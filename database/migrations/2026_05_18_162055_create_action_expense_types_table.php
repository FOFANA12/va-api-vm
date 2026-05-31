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
        Schema::create('action_expense_types', function (Blueprint $table) {
            $table->uuid('action_uuid');
            $table->uuid('expense_type_uuid');
            $table->decimal('total', 24, 2)->default(0);
            $table->timestamps();

            $table->primary(['action_uuid', 'expense_type_uuid']);
            $table->foreign('action_uuid')->references('uuid')->on('actions')->onDelete('cascade');
            $table->foreign('expense_type_uuid')->references('uuid')->on('expense_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_expense_types');
    }
};
