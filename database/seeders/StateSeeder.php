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
                'state_name' => 'Alabama',
                'state_image' => 'upload/state/alabama.jpg', // Example path, adjust if needed
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
             [
                'state_name' => 'Arizona',
                'state_image' => 'upload/state/arizona.jpg', // Example path, adjust if needed
                 'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
             [
                'state_name' => 'California',
                'state_image' => 'upload/state/california.jpg', // Example path, adjust if needed
                 'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
             [
                'state_name' => 'Florida',
                'state_image' => 'upload/state/florida.jpg', // Example path, adjust if needed
                 'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
             [
                'state_name' => 'New York',
                'state_image' => 'upload/state/newyork.jpg', // Example path, adjust if needed
                 'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],


         ]);
    }
}
