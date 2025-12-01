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
        Schema::create('action_fund_receipts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('action_uuid')->index();
            $table->string('reference', 100)->nullable()->unique();

            $table->date('receipt_date');
            $table->date('validity_date');

            $table->uuid('funding_source_uuid')->index();
            $table->uuid('currency_uuid')->index();

            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->decimal('amount_original', 14, 2)->index();
            $table->decimal('converted_amount', 14, 2)->index();
            $table->timestamps();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();


            $table->foreign('action_uuid')->references('uuid')->on('actions')->onDelete('restrict');
            $table->foreign('currency_uuid')->references('uuid')->on('currencies')->onDelete('restrict');
            $table->foreign('funding_source_uuid')->references('uuid')->on('funding_sources')->onDelete('restrict');

            $table->foreign('created_by')->references('uuid')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('uuid')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_fund_receipts');
    }
};
