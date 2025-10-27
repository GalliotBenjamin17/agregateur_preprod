<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('certifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->string('image')->nullable();
            $table->uuid('tenant_id')->nullable();
            $table->timestamps();
        });

        $certifications = [
            'Label Bas Carbone' => '/img/certifications/label-bas-carbone.png',
            'Dispositif CEE' => '/img/certifications/dispositif-cee.png',
        ];

        foreach ($certifications as $key => $value) {
            \App\Models\Certification::create([
                'name' => $key,
                'image' => $value,
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('certifications');
    }
};
