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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('slug');

            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('avatar');

            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();

            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('address_postal_code')->nullable();
            $table->string('address_city')->nullable();

            $table->timestamp('welcome_valid_until')->nullable();

            $table->dateTime('gdpr_consented_at')->nullable();
            $table->uuid('imported_by')->nullable();

            $table->uuid('tenant_id')->nullable();

            $table->boolean('is_shareholder')->default(false);

            $table->string('iban')->nullable();
            $table->string('bic')->nullable();

            $table->boolean('can_be_notified_transactional')->default(false);
            $table->boolean('can_be_notified_marketing')->default(false);
            $table->boolean('can_be_displayed_on_website')->default(false);

            $table->string('old_id')->nullable();

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
