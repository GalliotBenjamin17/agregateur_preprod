<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('address_1')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('data_policy_url')->nullable();
            $table->string('email_banner')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('phone')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'address_1',
                'postal_code',
                'city',
                'data_policy_url',
                'email_banner',
                'sender_email',
            ]);
        });
    }
};
