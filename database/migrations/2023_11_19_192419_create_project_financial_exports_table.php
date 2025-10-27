<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_financial_exports', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('project_id');

            $table->string('file_path')->nullable();

            $table->dateTime('generated_at')->nullable();
            $table->uuid('generated_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_financial_exports');
    }
};
