<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('donation_splits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('donation_id');
            $table->uuid('donation_split_id')->nullable();
            $table->float('amount');
            $table->uuid('split_by');
            $table->uuid('project_id');
            $table->text('description')->nullable();
            $table->uuid('project_carbon_price_id');
            $table->float('tonne_co2');

            $table->string('certificate_pdf_path')->nullable();
            $table->dateTime('certificate_pdf_generated_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('donation_splits');
    }
};
