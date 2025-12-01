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
        Schema::create('strategic_elements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('structure_uuid');
            $table->uuid('strategic_map_uuid');

            $table->uuid('parent_structure_uuid')->nullable();
            $table->uuid('parent_map_uuid')->nullable();
            $table->uuid('parent_element_uuid')->nullable();

            $table->enum('type', ['AXIS', 'LEVER'])->default('AXIS');
            $table->integer('order')->default(0)->unsigned();
            $table->string('name', 200);
            $table->string('abbreviation', 20);
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['strategic_map_uuid', 'name', 'type']);
            $table->unique(['strategic_map_uuid', 'abbreviation', 'type']);

            $table->foreign('parent_structure_uuid')->references('uuid')->on('structures')->onDelete('set null');
            $table->foreign('parent_map_uuid')->references('uuid')->on('strategic_maps')->onDelete('set null');

            $table->foreign('structure_uuid')->references('uuid')->on('structures')->onDelete('restrict');
            $table->foreign('strategic_map_uuid')->references('uuid')->on('strategic_maps')->onDelete('restrict');
            $table->foreign('created_by')->references('uuid')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('uuid')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strategic_elements');
    }
};
