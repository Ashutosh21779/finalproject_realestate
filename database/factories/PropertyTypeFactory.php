<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PropertyType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PropertyType>
 */
class PropertyTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type_name' => fake()->randomElement([
                'Apartment',
                'House',
                'Villa',
                'Condo',
                'Townhouse',
                'Studio',
                'Duplex',
                'Penthouse',
                'Bungalow',
                'Flat'
            ]),
            'type_icon' => fake()->randomElement([
                'icon-1',
                'icon-2', 
                'icon-3',
                'icon-4',
                'icon-5'
            ]),
        ];
    }
}
