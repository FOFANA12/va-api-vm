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
        Schema::create('strategic_domains', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('reference', 100)->unique()->nullable();
            $table->string('name', 100)->unique();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget', 14, 2)->default(0);
            $table->uuid('action_domain_uuid')->nullable();
            $table->string('currency', 10)->default('MRU');
            $table->uuid('responsible_uuid')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->string('status', 50)->default('preparation')->nullable();
            $table->timestamp('status_changed_at')->nullable();
            $table->uuid('status_changed_by')->nullable();

            $table->string('state', 50)->default('none')->nullable();
            $table->timestamp('state_changed_at')->nullable();
            $table->uuid('state_changed_by')->nullable();

            $table->text('description')->nullable();
            $table->text('prerequisites')->nullable();
            $table->text('impacts')->nullable();
            $table->text('risks')->nullable();

            $table->foreign('action_domain_uuid')->references('uuid')->on('action_domains')->onDelete('restrict');
            $table->foreign('responsible_uuid')->references('uuid')->on('users')->onDelete('restrict');
            $table->foreign('created_by')->references('uuid')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('uuid')->on('users')->onDelete('set null');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strategic_domains');
    }
};
