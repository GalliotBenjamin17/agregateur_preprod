<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'domain' => \Str::slug($this->faker->word()),
            'logo' => $this->faker->imageUrl(),
            'login_image' => $this->faker->imageUrl(1200, 1200),
            'public_url' => $this->faker->url(),
            'created_by' => $this->faker->word(),
            'default_commission' => 10,
            'price_tco2' => $this->faker->randomFloat(),
            'interface_title' => $this->faker->text(50),
            'interface_subtitle' => $this->faker->text(200),
            'primary_color' => $this->faker->hexColor(),
            'primary_color_text' => $this->faker->hexColor(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
