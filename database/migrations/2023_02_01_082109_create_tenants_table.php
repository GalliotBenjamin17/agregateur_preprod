<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->string('domain');
            $table->string('logo');
            $table->string('login_image')->nullable();
            $table->string('public_url')->nullable();
            $table->uuid('created_by');
            $table->float('default_commission')->nullable();
            $table->float('price_tco2')->nullable();

            $table->text('interface_title')->nullable();
            $table->text('interface_subtitle')->nullable();
            $table->string('primary_color')->nullable();
            $table->string('primary_color_text')->nullable();

            $table->string('payzen_user_id')->nullable();
            $table->text('payzen_password_test')->nullable();
            $table->text('payzen_password_prod')->nullable();

            $table->uuid('default_organization_id')->nullable();

            $table->boolean('payments_mode_test')->default(true);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tenants');
    }
};
