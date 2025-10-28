<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Créer une nouvelle colonne DATETIME temporaire
        Schema::table('projects', function (Blueprint $table) {
            $table->dateTime('planned_audit_at')->nullable()->after('planned_audit_year');
        });

        // 2) Copier les années int -> DATETIME (YYYY-01-01 00:00:00)
        DB::statement("
            UPDATE projects
            SET planned_audit_at = STR_TO_DATE(CONCAT(planned_audit_year, '-01-01 00:00:00'), '%Y-%m-%d %H:%i:%s')
            WHERE planned_audit_year IS NOT NULL
        ");

        // 3) Supprimer l’ancienne colonne int
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('planned_audit_year');
        });

        // 4) Renommer la colonne temporaire -> nom final
        Schema::table('projects', function (Blueprint $table) {
            $table->renameColumn('planned_audit_at', 'planned_audit_year');
        });
    }

    public function down(): void
    {
        // 1) Recréer une colonne int temporaire
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('planned_audit_year_int')->nullable()->after('planned_audit_year');
        });

        // 2) Extraire l'année du DATETIME -> int
        DB::statement("
            UPDATE projects
            SET planned_audit_year_int = YEAR(planned_audit_year)
            WHERE planned_audit_year IS NOT NULL
        ");

        // 3) Supprimer la colonne DATETIME
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('planned_audit_year');
        });

        // 4) Renommer la colonne int temporaire vers le nom d'origine
        Schema::table('projects', function (Blueprint $table) {
            $table->renameColumn('planned_audit_year_int', 'planned_audit_year');
        });
    }
};
