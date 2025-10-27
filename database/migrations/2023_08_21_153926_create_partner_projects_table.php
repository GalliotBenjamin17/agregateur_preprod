<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_projects', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('project_id');
            $table->uuid('partner_id');

            $table->string('commission_type');

            $table->float('commission_percentage')->nullable();
            $table->float('commission_numerical', 10, 2)->nullable();

            $table->text('indications')->nullable();

            $table->uuid('created_by');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_projects');
    }
};
