<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('contributor_space_banner_title')->nullable()->change();
            $table->text('contributor_space_banner_description')->nullable()->change();
            $table->string('contributor_space_banner_picture')->nullable()->change();
            $table->string('contributor_space_banner_button_text')->nullable()->change();
            $table->string('contributor_space_banner_button_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('contributor_space_banner_title')->nullable(false)->change();
            $table->text('contributor_space_banner_description')->nullable(false)->change();
            $table->string('contributor_space_banner_picture')->nullable(false)->change();
            $table->string('contributor_space_banner_button_text')->nullable(false)->change();
            $table->string('contributor_space_banner_button_url')->nullable(false)->change();
        });
    }
};
