<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_carbon_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->float('price');
            $table->uuid('created_by');
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->boolean('sync_with_tenant')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_carbon_prices');
    }
};
