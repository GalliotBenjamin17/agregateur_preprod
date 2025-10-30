<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('user_table_preferences')) {
            return;
        }

        if (! Schema::hasColumn('user_table_preferences', 'saved_filters')) {
            Schema::table('user_table_preferences', function (Blueprint $table) {
                $table->json('saved_filters')->nullable()->after('toggled_columns');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_table_preferences') && Schema::hasColumn('user_table_preferences', 'saved_filters')) {
            Schema::table('user_table_preferences', function (Blueprint $table) {
                $table->dropColumn('saved_filters');
            });
        }
    }
};

