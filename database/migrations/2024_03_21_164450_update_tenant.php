<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('logo_white')->nullable();

            $table->boolean('contributor_space_banner_activated')->default(false);
            $table->string('contributor_space_banner_title');
            $table->text('contributor_space_banner_description');
            $table->string('contributor_space_banner_picture');
            $table->string('contributor_space_banner_button_text');
            $table->string('contributor_space_banner_button_url');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'logo_white',
                'contributor_space_banner_activated',
                'contributor_space_banner_title',
                'contributor_space_banner_description',
                'contributor_space_banner_picture',
                'contributor_space_banner_button_text',
                'contributor_space_banner_button_url',
            ]);
        });
    }
};
