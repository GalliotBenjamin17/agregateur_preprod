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
        Schema::create('idea_comment_votes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('idea_comment_id');
            $table->enum('type', [
                'plus',
                'minus',
            ]);
            $table->string('featured_image')->nullable();
            $table->string('detailed_image')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'idea_comment_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('idea_comment_votes');
    }
};
