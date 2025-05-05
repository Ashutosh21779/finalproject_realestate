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

// Update properties with missing coordinates
$updated = DB::table('properties')
    ->whereNull('latitude')
    ->orWhere('latitude', '')
    ->orWhereNull('longitude')
    ->orWhere('longitude', '')
    ->update([
        'latitude' => $defaultLat,
        'longitude' => $defaultLng
    ]);

echo "Updated $updated properties with default coordinates for Kathmandu, Nepal.\n";
echo "Default latitude: $defaultLat\n";
echo "Default longitude: $defaultLng\n";
