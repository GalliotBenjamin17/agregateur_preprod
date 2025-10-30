<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('user_table_preferences')) {
            Schema::create('user_table_preferences', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('table_key');
                $table->json('toggled_columns')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'table_key']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_table_preferences');
    }
};
