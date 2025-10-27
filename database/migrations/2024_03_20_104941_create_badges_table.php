<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('slug');

            $table->string('picture')->nullable();

            $table->text('description')->nullable();

            $table->uuid('tenant_id');

            $table->timestamps();
        });

        Schema::create('badge_organizations', function (Blueprint $table) {
            $table->uuid('badge_id');
            $table->uuid('organization_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
        Schema::dropIfExists('badge_organizations');
    }
};
