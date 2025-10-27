<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('tenant_commission_type')->nullable();
            $table->float('tenant_commission_percentage')->nullable();
            $table->float('tenant_commission_numerical')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'tenant_commission_type',
                'tenant_commission_percentage',
                'tenant_commission_numerical',
            ]);
        });
    }
};
