<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'      => fake()->company(),
            'slug'      => fake()->slug(),
            // 'logo' => fake()->imageUrl(),
            'address'   => fake()->address(),
            'city'      => fake()->city(),
            'pincode'   => fake()->postcode(),
            'phone'     => fake()->phoneNumber(),
            'email'     => fake()->companyEmail(),
            'website'   => fake()->url(),
            'additional_info' => fake()->paragraph(),
            'description'     => fake()->sentence(),
            'is_active'       => fake()->boolean(),
        ];
    }
}