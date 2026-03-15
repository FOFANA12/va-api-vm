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
        if (!Schema::hasColumn('strategic_elements', 'reference')) {

            Schema::table('strategic_elements', function (Blueprint $table) {
                $table->string('reference', 100)->nullable()->unique()->after('abbreviation');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('strategic_elements', 'reference')) {

            Schema::table('strategic_elements', function (Blueprint $table) {
                $table->dropUnique(['reference']);
                $table->dropColumn('reference');
            });
        }
    }
};
