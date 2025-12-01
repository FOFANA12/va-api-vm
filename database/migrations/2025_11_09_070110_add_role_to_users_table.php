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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('role_uuid')->nullable();

            $table->foreign('role_uuid')->references('uuid')->on('roles')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['role_uuid']);

            // Drop columns
            $table->dropColumn(['role_uuid']);
        });
    }
};
