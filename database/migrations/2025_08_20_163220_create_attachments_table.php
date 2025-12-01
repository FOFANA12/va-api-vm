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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('attachable_type', 100);
            $table->integer('attachable_id');
            $table->string('title', 255);
            $table->string('original_name', 255);
            $table->string('identifier', 60);
            $table->string('mime_type', 100)->nullable();
            $table->bigInteger('size')->nullable();
            $table->uuid('uploaded_by')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->text('comment')->nullable();
            $table->uuid('file_type_uuid')->nullable();

            $table->index(['attachable_id', 'attachable_type'], 'attachments_attachable_index');

            $table->foreign('file_type_uuid')->references('uuid')->on('file_types')->onDelete('restrict');
            $table->foreign('uploaded_by')->references('uuid')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
