<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('site_settings')->insert([
            [
                'id' => 1, // Ensure the ID is 1 as the code uses find(1)
                'support_phone' => '1234567890',
                'company_address' => '123 Main St, Anytown, USA',
                'email' => 'info@example.com',
                'facebook' => 'https://facebook.com',
                'twitter' => 'https://twitter.com',
                'logo' => 'upload/logo/logo.png', // Example path
                'copyright' => '© ' . date('Y') . ' Your Company Name. All rights reserved.', // Example copyright
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]

         ]);
    }
}
