<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('created_by')->nullable()->default(null);
            $table->uuid('updated_by')->nullable()->default(null);

            $table->foreign('created_by')->references('uuid')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('uuid')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop columns
            $table->dropColumn(['created_by', 'updated_by']);
        });
    }
};
