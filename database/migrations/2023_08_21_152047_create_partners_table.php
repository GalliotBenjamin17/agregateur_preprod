<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('slug');

            $table->string('avatar')->nullable();

            $table->uuid('tenant_id');

            $table->json('contacts')->nullable();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->string('billing_address')->nullable();
            $table->string('billing_address_2')->nullable();
            $table->string('billing_address_zip_code')->nullable();
            $table->string('billing_address_city')->nullable();
            $table->string('billing_email')->nullable();

            $table->string('legal_siret')->nullable();
            $table->string('legal_siren')->nullable();
            $table->string('legal_created_at')->nullable();
            $table->string('legal_name')->nullable();
            $table->string('legal_activity_code')->nullable();

            $table->uuid('created_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
