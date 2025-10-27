<?php

use App\Enums\Models\News\NewsStateEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('slug');
            $table->longText('content')->nullable();
            $table->string('state')->default(NewsStateEnum::Draft->databaseKey());

            $table->uuid('project_id');
            $table->uuid('tenant_id');

            $table->boolean('is_featured')->default(false);

            $table->dateTime('scheduled_at')->nullable();

            $table->uuid('author_id')->nullable();
            $table->uuid('created_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
