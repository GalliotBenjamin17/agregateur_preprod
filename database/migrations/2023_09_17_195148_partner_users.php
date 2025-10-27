<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_users', function (Blueprint $table) {
            $table->uuid('partner_id');
            $table->uuid('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_users');
    }
};
