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
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');

            $table->string('avatar')->nullable();
            $table->longText('description')->nullable();
            $table->json('contacts')->nullable();
            $table->uuid('organization_type_id')->nullable();
            $table->uuid('created_by');

            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('address_postal_code')->nullable();
            $table->string('address_city')->nullable();

            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();

            $table->string('iban')->nullable();

            $table->uuid('organization_parent_id')->nullable();
            $table->uuid('tenant_id')->nullable();

            $table->boolean('can_be_displayed_on_website')->default(false);
            $table->boolean('is_shareholder')->default(false);

            $table->string('legal_siret')->nullable();
            $table->string('legal_siren')->nullable();
            $table->string('legal_created_at')->nullable();
            $table->string('legal_name')->nullable();
            $table->string('legal_activity_code')->nullable();
            $table->boolean('legal_is_ess')->default(false);

            $table->string('billing_email')->nullable();

            $table->uuid('manager_id')->nullable();

            $table->string('old_id')->nullable();

            $table->timestamps();
        });

        Schema::create('organization_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        });

        Schema::create('organization_type_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->uuid('organization_type_id');
            $table->timestamps();
        });

        Schema::create('user_organizations', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->uuid('organization_id');
            $table->uuid('organization_type_link_id')->nullable();
            $table->boolean('is_organization_manager')->default(false);
        });

        $organizationTypes = [
            'Associations' => [
                'Gérant',
                'Employé',
                'Bénévole',
                'Donateur',
            ],
            'ONG' => [
                'Gérant',
                'Employé',
                'Bénévole',
                'Donateur',
            ],
            'Entreprise' => [
                'Gérant',
                'Représentant public',
                'Employé',
            ],
            'Syndicat privé' => [
                'Président',
                'Directeur',
                'Représentant public',
                'Adhérant',
            ],
            'Syndicat public' => [
                'Président',
                'Directeur',
                'Représentant public',
                'Adhérant',
                'Élu',
            ],
            'Conseil scolaire' => [
                "Chef d'établissement",
                'CPE',
                'Enseignant',
                'Personnel encadrant',
                'Assistant(e) sociale',
            ],
            "Groupement d'intérêt" => [
                'Élu',
                'Lobbyiste',
            ],
            'Communauté religieuse' => [
                'Chef de communauté',
                'Bénévole',
            ],
        ];

        foreach ($organizationTypes as $key => $values) {
            $uuid = \Illuminate\Support\Str::orderedUuid();
            DB::table('organization_types')->insert([
                [
                    'id' => $uuid,
                    'name' => $key,
                    'slug' => \Illuminate\Support\Str::slug($key),
                ],
            ]);
            foreach ($values as $value) {
                DB::table('organization_type_links')->insert([
                    [
                        'id' => \Illuminate\Support\Str::orderedUuid(),
                        'name' => $value,
                        'slug' => \Illuminate\Support\Str::slug($value),
                        'organization_type_id' => $uuid,
                    ],
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('organization_types');
        Schema::dropIfExists('organization_type_links');
        Schema::dropIfExists('user_organizations');
    }
};
