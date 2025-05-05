<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Property;
use Illuminate\Support\Facades\DB;

class UpdatePropertyCoordinates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'property:update-coordinates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update missing or invalid property coordinates with default values for Kathmandu, Nepal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Default coordinates for Kathmandu, Nepal
        $defaultLat = 27.7172;
        $defaultLng = 85.3240;

        $this->info('Checking for properties with missing coordinates...');

        // Count properties with missing coordinates
        $missingCoordinates = DB::table('properties')
            ->where(function($query) {
                $query->whereNull('latitude')
                    ->orWhere('latitude', '')
                    ->orWhereNull('longitude')
                    ->orWhere('longitude', '');
            })
            ->count();

        $this->info("Found {$missingCoordinates} properties with missing coordinates.");

        if ($missingCoordinates > 0) {
            // Update properties with missing coordinates
            $updated = DB::table('properties')
                ->where(function($query) {
                    $query->whereNull('latitude')
                        ->orWhere('latitude', '')
                        ->orWhereNull('longitude')
                        ->orWhere('longitude', '');
                })
                ->update([
                    'latitude' => $defaultLat,
                    'longitude' => $defaultLng
                ]);

            $this->info("Updated {$updated} properties with default coordinates for Kathmandu, Nepal.");
            $this->info("Default latitude: {$defaultLat}");
            $this->info("Default longitude: {$defaultLng}");
        } else {
            $this->info("All properties already have coordinates. No updates needed.");
        }

        $this->info('Checking for properties with invalid coordinates...');

        // Check for properties with invalid coordinates (non-numeric values)
        $invalidCoordinates = DB::table('properties')
            ->where(function($query) {
                $query->whereRaw("CAST(latitude AS DECIMAL(10,6)) IS NULL")
                    ->orWhereRaw("CAST(longitude AS DECIMAL(10,6)) IS NULL");
            })
            ->count();

        if ($invalidCoordinates > 0) {
            $this->info("Found {$invalidCoordinates} properties with invalid (non-numeric) coordinates.");
            
            // Update properties with invalid coordinates
            $updated = DB::table('properties')
                ->where(function($query) {
                    $query->whereRaw("CAST(latitude AS DECIMAL(10,6)) IS NULL")
                        ->orWhereRaw("CAST(longitude AS DECIMAL(10,6)) IS NULL");
                })
                ->update([
                    'latitude' => $defaultLat,
                    'longitude' => $defaultLng
                ]);
                
            $this->info("Updated {$updated} properties with invalid coordinates to use default values.");
        } else {
            $this->info("All properties have valid numeric coordinates.");
        }

        $this->info("Command completed successfully.");
        
        return Command::SUCCESS;
    }
}
