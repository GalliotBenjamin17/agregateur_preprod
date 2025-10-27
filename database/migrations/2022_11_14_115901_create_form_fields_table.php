<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('question');
            $table->uuid('form_id');
            $table->text('description')->nullable();
            $table->boolean('is_optional')->default(true);
            $table->string('type');

            $table->integer('stars_count')->default(5);
            $table->json('choices')->nullable();
            $table->integer('minimum_choices')->nullable();
            $table->integer('maximum_choices')->nullable();

            $table->timestamps();
        });

        Schema::create('form_field_options', function (Blueprint $table) {
            $table->uuid('form_field_id');
            $table->uuid('option_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_fields');
        Schema::dropIfExists('form_field_options');
    }
};
