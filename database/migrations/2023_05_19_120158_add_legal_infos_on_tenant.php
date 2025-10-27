<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('siret')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('cgu')->nullable();
            $table->string('cgv')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'siret',
                'vat_number',
                'cgu',
                'cgv',
            ]);
        });
    }
};
