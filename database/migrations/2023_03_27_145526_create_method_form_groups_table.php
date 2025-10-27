<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('method_form_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->uuid('created_by');
            $table->uuid('active_method_form_id')->nullable();
            $table->uuid('segmentation_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('method_form_groups');
    }
};
