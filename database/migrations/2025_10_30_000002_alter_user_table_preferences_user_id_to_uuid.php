<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('user_table_preferences')) {
            return;
        }

        // Ensure column exists
        if (! Schema::hasColumn('user_table_preferences', 'user_id')) {
            Schema::table('user_table_preferences', function (Blueprint $table) {
                $table->uuid('user_id')->after('id');
            });
        }

        // Try to drop existing foreign key if any (name may vary)
        try {
            Schema::table('user_table_preferences', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } catch (\Throwable $e) {
            // ignore if not present
        }

        // Change column type to CHAR(36) to match users.uuid
        try {
            DB::statement('ALTER TABLE `user_table_preferences` MODIFY `user_id` CHAR(36) NOT NULL');
        } catch (\Throwable $e) {
            // Some MySQL variants require USING clause only when converting numeric -> char, but above works generally.
        }

        // Add (or re-add) FK to users(id)
        try {
            DB::statement('ALTER TABLE `user_table_preferences` ADD CONSTRAINT `user_table_preferences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE');
        } catch (\Throwable $e) {
            // If it already exists, ignore
        }

        // Add saved_filters column if missing
        if (! Schema::hasColumn('user_table_preferences', 'saved_filters')) {
            Schema::table('user_table_preferences', function (Blueprint $table) {
                $table->json('saved_filters')->nullable()->after('toggled_columns');
            });
        }
    }

    public function down(): void
    {
        // No-op: we won't convert UUIDs back to bigint.
    }
};
