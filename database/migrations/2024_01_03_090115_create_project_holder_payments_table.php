<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_holder_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('project_id');

            $table->float('amount');
            $table->float('amount_ht');

            $table->string('receipt')->nullable();

            $table->uuid('created_by');

            $table->integer('order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_holder_payments');
    }
};
