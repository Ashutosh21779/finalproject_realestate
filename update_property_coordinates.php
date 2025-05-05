<?php
// This script updates any properties without latitude and longitude
// with default coordinates for Kathmandu, Nepal

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use Illuminate\Support\Facades\DB;

// Default coordinates for Kathmandu, Nepal
$defaultLat = 27.7172;
$defaultLng = 85.3240;

// Count properties with missing coordinates
$missingCoordinates = DB::table('properties')
    ->where(function($query) {
        $query->whereNull('latitude')
            ->orWhere('latitude', '')
            ->orWhereNull('longitude')
            ->orWhere('longitude', '');
    })
    ->count();

echo "Found $missingCoordinates properties with missing coordinates.\n";

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

    echo "Updated $updated properties with default coordinates for Kathmandu, Nepal.\n";
    echo "Default latitude: $defaultLat\n";
    echo "Default longitude: $defaultLng\n";
} else {
    echo "All properties already have coordinates. No updates needed.\n";
}

// Check for properties with invalid coordinates (non-numeric values)
$invalidCoordinates = DB::table('properties')
    ->where(function($query) {
        $query->whereRaw("CAST(latitude AS DECIMAL(10,6)) IS NULL")
            ->orWhereRaw("CAST(longitude AS DECIMAL(10,6)) IS NULL");
    })
    ->count();

if ($invalidCoordinates > 0) {
    echo "Found $invalidCoordinates properties with invalid (non-numeric) coordinates.\n";
    
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
        
    echo "Updated $updated properties with invalid coordinates to use default values.\n";
} else {
    echo "All properties have valid numeric coordinates.\n";
}

echo "Script completed successfully.\n";
