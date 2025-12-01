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
        Schema::table('strategic_elements', function (Blueprint $table) {
            $table->foreign('parent_element_uuid')
                ->references('uuid')
                ->on('strategic_elements')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('strategic_elements', function (Blueprint $table) {
            $table->dropForeign(['parent_element_uuid']);
        });
    }
};
