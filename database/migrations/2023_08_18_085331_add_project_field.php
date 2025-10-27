<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('dreal_reference')->nullable();
            $table->string('credit_temporality')->nullable();
            $table->string('credit_characteristics')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'dreal_reference',
                'credit_temporality',
                'credit_characteristics',
            ]);
        });
    }
};
