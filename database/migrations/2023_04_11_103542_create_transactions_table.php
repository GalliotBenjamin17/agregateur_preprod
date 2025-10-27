<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('project_id')->nullable();
            $table->uuidMorphs('related');

            $table->uuid('donation_id')->nullable();

            $table->string('payment_order_id');
            $table->string('order_id');
            $table->string('payment_url');

            $table->integer('amount');
            $table->integer('tax_amount');
            $table->string('currency')->default('EUR');

            $table->enum('status', [
                'PAID',
                'UNPAID',
                'RUNNING',
                'PARTIALLY_PAID',
                'ABANDONED',
            ]);

            $table->enum('category', [
                'PRIVATE',
                'COMPANY',
            ]);

            $table->string('form_action')->default('PAYMENT');
            $table->string('merchant_comment')->default('Coop Carbone');

            $table->string('customer_reference')->nullable();
            $table->string('customer_email')->nullable();

            $table->json('shopping_cart')->nullable();
            $table->json('channel_options')->nullable();

            $table->dateTime('expiration_at');

            $table->uuid('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
