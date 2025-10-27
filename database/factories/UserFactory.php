<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'slug' => $this->faker->slug(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt($this->faker->password()),
            'avatar' => $this->faker->imageUrl(),
            'gender' => $this->faker->randomElement(['m', 'f']),
            'address_1' => $this->faker->streetAddress(),
            'address_postal_code' => $this->faker->postcode(),
            'address_city' => $this->faker->city(),
            'welcome_valid_until' => Carbon::now(),
            'gdpr_consented_at' => Carbon::now(),
            'imported_by' => $this->faker->word(),
            'is_shareholder' => $this->faker->boolean(),
            'can_be_notified_transactional' => $this->faker->boolean(),
            'can_be_notified_marketing' => $this->faker->boolean(),
            'can_be_displayed_on_website' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
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
