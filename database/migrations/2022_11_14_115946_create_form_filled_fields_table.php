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
        Schema::create('form_filled_fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('form_filled_id');
            $table->uuid('form_field_id');
            $table->boolean('is_checked')->nullable();
            $table->text('text_value')->nullable();
            $table->integer('numeric_value')->nullable();
            $table->uuid('form_option_id')->nullable();
            $table->boolean('is_public')->default(false);
            $table->json('options')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_filled_fields');
    }
};
