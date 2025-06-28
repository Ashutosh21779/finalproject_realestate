<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\State;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\State>
 */
class StateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'state_name' => fake()->randomElement([
                'Kathmandu',
                'Pokhara',
                'Lalitpur',
                'Bhaktapur',
                'Chitwan',
                'Butwal',
                'Dharan',
                'Biratnagar',
                'Janakpur',
                'Nepalgunj'
            ]),
            'state_image' => 'upload/state/' . fake()->randomElement([
                'kathmandu.jpg',
                'pokhara.jpg',
                'lalitpur.jpg',
                'bhaktapur.jpg',
                'chitwan.jpg'
            ]),
        ];
    }
}
