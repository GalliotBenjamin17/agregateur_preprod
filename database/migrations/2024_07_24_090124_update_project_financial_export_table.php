<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_financial_exports', function (Blueprint $table) {
            $table->uuid('project_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('project_financial_exports', function (Blueprint $table) {
            $table->uuid('project_id')->nullable(false)->change();
        });
    }
};
