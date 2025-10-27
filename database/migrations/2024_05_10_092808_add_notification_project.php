<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('is_semi_financed_notification_sent')->default(false);
            $table->boolean('is_fully_financed_notification_sent')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'is_semi_financed_notification_sent',
                'is_fully_financed_notification_sent',
            ]);
        });
    }
};
