<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('segmentations', function (Blueprint $table) {
            $table->string('chart_color')->default('#f6f8fa');
            $table->integer('chart_spread_years')->default(5);
        });
    }

    public function down(): void
    {
        Schema::table('segmentations', function (Blueprint $table) {
            $table->dropColumn([
                'chart_color',
                'chart_spread_years',
            ]);
        });
    }
};
