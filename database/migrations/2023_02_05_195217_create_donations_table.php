<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('source', [
                'website',
                'terminal',
                'bank_account',
                'cash',
                'other',
            ]);
            $table->text('external_id')->nullable();
            $table->json('source_informations')->nullable();
            $table->json('transaction_informations')->nullable();
            $table->nullableUuidMorphs('related');
            $table->float('amount');
            $table->uuid('created_by')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('is_donation_splits_full')->default(false);

            $table->text('bill_file')->nullable();
            $table->string('bill_reference')->nullable();

            $table->string('certificate_pdf_path')->nullable();
            $table->dateTime('certificate_pdf_generated_at')->nullable();

            $table->uuid('tenant_id');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('donations');
    }
};
