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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('company_name', 255);
            $table->string('tax_number', 20)->unique();
            $table->string('register_number', 50)->nullable();
            $table->year('establishment_year')->nullable();
            $table->decimal('capital', 14, 2)->default(0);
            $table->decimal('annual_turnover', 14, 2)->default(0);
            $table->integer('employees_count')->default(0);
            $table->decimal('note', 3, 2)->default(0);
            $table->boolean('status')->default(true);

            $table->uuid('contract_type_uuid');
            $table->string('name', 100);
            $table->string('phone', 20);
            $table->string('whatsapp', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('address', 100)->nullable();

            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('contract_type_uuid')->references('uuid')->on('contract_types')->onDelete('restrict');
            $table->foreign('created_by')->references('uuid')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('uuid')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
