<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB facade
use Carbon\Carbon; // Import Carbon for timestamps

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('states')->insert([
            [
                'state_name' => 'Kathmandu',
                'state_image' => 'upload/state/kathmandu.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
             [
                'state_name' => 'Pokhara',
                'state_image' => 'upload/state/pokhara.jpg',
                 'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
             [
                'state_name' => 'Lalitpur',
                'state_image' => 'upload/state/lalitpur.jpg',
                 'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
             [
                'state_name' => 'Bhaktapur',
                'state_image' => 'upload/state/bhaktapur.jpg',
                 'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
             [
                'state_name' => 'Chitwan',
                'state_image' => 'upload/state/chitwan.jpg',
                 'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
             [
                'state_name' => 'Butwal',
                'state_image' => 'upload/state/butwal.jpg',
                 'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
             [
                'state_name' => 'Biratnagar',
                'state_image' => 'upload/state/biratnagar.jpg',
                 'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],


         ]);
    }
}
