<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->float('holder_amount_give')->nullable();
            $table->boolean('is_holder_amount_give')->nullable();
            $table->json('holder_amount_documents')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'holder_amount_give',
                'is_holder_amount_give',
                'holder_amount_documents',
            ]);
        });
    }
};
