<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_project_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('partner_project_id');
            $table->string('payment_state');
            $table->float('amount');

            $table->string('receipt')->nullable();

            $table->dateTime('validated_at')->nullable();
            $table->uuid('validated_by')->nullable();

            $table->uuid('created_by');

            $table->integer('order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_project_payments');
    }
};
