<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug(),
            'avatar' => $this->faker->word(),
            'description' => $this->faker->text(),
            'contacts' => $this->faker->words(),
            'address_1' => $this->faker->address(),
            'address_2' => $this->faker->address(),
            'address_postal_code' => $this->faker->postcode(),
            'address_city' => $this->faker->city(),
            'can_be_displayed_on_website' => $this->faker->boolean(),
            'is_shareholder' => $this->faker->boolean(),
            'legal_siret' => $this->faker->word(),
            'legal_siren' => $this->faker->word(),
            'legal_created_at' => Carbon::now(),
            'legal_name' => $this->faker->name(),
            'legal_activity_code' => $this->faker->word(),
            'legal_is_ess' => $this->faker->boolean(),
            'billing_email' => $this->faker->unique()->safeEmail(),
            'manager_id' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'organization_type_id' => null,
            'created_by' => User::factory(),
        ];
    }

    public function withTenant(Tenant $tenant)
    {
        return $this->state(function () use ($tenant) {
            return [
                'tenant_id' => $tenant->id,
            ];
        });
    }
}
