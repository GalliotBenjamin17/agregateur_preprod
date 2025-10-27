<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');

            $table->longText('summary')->nullable();
            $table->longText('description')->nullable();

            $table->uuid('tenant_id')->nullable();
            $table->uuid('certification_id')->nullable();

            $table->string('thumbnail')->nullable();
            $table->string('featured_image')->nullable();

            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('address_postal_code')->nullable();
            $table->string('address_city')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();

            $table->text('goal_text')->nullable();
            $table->date('start_at')->nullable();

            $table->integer('duration')->nullable();

            $table->float('cost_global_ttc', 16)->nullable();
            $table->float('tco2', 16)->nullable();
            $table->integer('cost_duration_years')->nullable();
            $table->float('amount_wanted_ttc', 16)->nullable();
            $table->float('amount_wanted', 16)->nullable();
            $table->float('cost_commission', 16)->nullable();

            $table->boolean('can_be_displayed_on_website')->default(false);
            $table->boolean('can_be_financed_online')->default(false);
            $table->boolean('can_be_displayed_percentage_of_funding')->default(false);
            $table->boolean('can_be_displayed_on_terminal')->default(false);

            $table->uuidMorphs('sponsor');
            $table->uuid('referent_id')->nullable();
            $table->uuid('auditor_id')->nullable();
            $table->uuid('created_by');

            $table->string('state');
            $table->string('certification_state');

            $table->uuid('method_form_id')->nullable();
            $table->json('method_replies')->nullable();

            $table->string('sub_project_type')->nullable();
            $table->uuid('parent_project_id')->nullable();
            $table->integer('sub_project_year')->nullable();

            $table->uuid('segmentation_id')->nullable();

            $table->string('old_id')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
