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
        Schema::create('gdpr_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email');
            $table->longText('code');
            $table->enum('type', [
                'see',
                'download',
                'delete',
            ]);
            $table->dateTimeTz('send_at');
            $table->dateTimeTz('expires_at');
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
        Schema::dropIfExists('gdpr_requests');
    }
};
