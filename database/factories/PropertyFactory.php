<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\State;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $propertyName = fake()->randomElement([
            'Luxury Apartment in Kathmandu',
            'Modern Villa in Pokhara',
            'Cozy House in Lalitpur',
            'Spacious Flat in Bhaktapur',
            'Beautiful Condo in Chitwan',
            'Executive Townhouse',
            'Premium Penthouse',
            'Family Bungalow',
            'Studio Apartment',
            'Duplex House'
        ]);

        $slug = strtolower(str_replace(' ', '-', $propertyName)) . '-' . fake()->unique()->numberBetween(1000, 9999);
        
        return [
            'ptype_id' => PropertyType::factory(),
            'amenities_id' => '1,2,3', // Default amenities
            'property_name' => $propertyName,
            'property_slug' => $slug,
            'property_code' => 'PC' . fake()->unique()->numberBetween(10000, 99999),
            'property_status' => fake()->randomElement(['rent', 'buy']),
            'lowest_price' => fake()->numberBetween(5000000, 50000000), // NPR prices
            'max_price' => fake()->numberBetween(50000000, 100000000),
            'property_thambnail' => 'upload/property/thumbnail/' . fake()->randomElement([
                'property1.jpg',
                'property2.jpg', 
                'property3.jpg',
                'property4.jpg',
                'property5.jpg'
            ]),
            'short_descp' => fake()->sentence(10),
            'long_descp' => fake()->paragraph(5),
            'bedrooms' => fake()->randomElement(['1', '2', '3', '4', '5']),
            'bathrooms' => fake()->randomElement(['1', '2', '3', '4']),
            'garage' => fake()->randomElement(['1', '2', '0']),
            'garage_size' => fake()->randomElement(['200', '300', '400', '500']),
            'property_size' => fake()->numberBetween(800, 5000),
            'property_video' => null,
            'address' => fake()->address,
            'city' => fake()->randomElement([
                'Kathmandu',
                'Pokhara', 
                'Lalitpur',
                'Bhaktapur',
                'Chitwan',
                'Butwal'
            ]),
            'state' => State::factory(),
            'postal_code' => fake()->postcode,
            'neighborhood' => fake()->randomElement([
                'Thamel',
                'Lakeside',
                'Patan',
                'Durbar Square',
                'New Baneshwor',
                'Sanepa'
            ]),
            'latitude' => fake()->latitude(27.6, 27.8), // Nepal coordinates
            'longitude' => fake()->longitude(85.2, 85.4),
            'featured' => fake()->randomElement(['0', '1']),
            'hot' => fake()->randomElement(['0', '1']),
            'agent_id' => User::factory(),
            'status' => '1', // Active
        ];
    }
}
