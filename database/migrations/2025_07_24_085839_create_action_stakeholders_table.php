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
        Schema::create('action_stakeholders', function (Blueprint $table) {
            $table->uuid('action_uuid');
            $table->uuid('stakeholder_uuid');

            $table->primary(['action_uuid', 'stakeholder_uuid']);
            $table->foreign('action_uuid')->references('uuid')->on('actions')->onDelete('cascade');
            $table->foreign('stakeholder_uuid')->references('uuid')->on('stakeholders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_stakeholders');
    }
};
