<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MapHelper
{
    /**
     * Get coordinates (latitude and longitude) from an address using Google Maps Geocoding API
     *
     * @param string $address The address to geocode
     * @return array|null Array with 'lat' and 'lng' keys, or null if geocoding failed
     */
    public static function getCoordinatesFromAddress($address)
    {
        try {
            // Google Maps Geocoding API key
            $apiKey = 'AIzaSyBwjrCx-T4UlUDmXALumCOWkJv-B7m9yCE';
            
            // URL encode the address
            $encodedAddress = urlencode($address);
            
            // Make the API request
            $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
                'address' => $encodedAddress,
                'key' => $apiKey
            ]);
            
            // Check if the request was successful
            if ($response->successful() && $response->json('status') === 'OK') {
                $results = $response->json('results');
                
                if (count($results) > 0) {
                    $location = $results[0]['geometry']['location'];
                    return [
                        'lat' => $location['lat'],
                        'lng' => $location['lng']
                    ];
                }
            }
            
            // Log the error if the request failed
            if ($response->json('status') !== 'OK') {
                Log::error('Google Maps Geocoding API error: ' . $response->json('status') . ' - ' . ($response->json('error_message') ?? 'Unknown error'));
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error geocoding address: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Generate a Google Maps Embed API URL for a location
     *
     * @param float $lat Latitude
     * @param float $lng Longitude
     * @param int $zoom Zoom level (1-20)
     * @param string $mapType Map type (roadmap, satellite, hybrid, terrain)
     * @return string The Google Maps Embed API URL
     */
    public static function getMapEmbedUrl($lat, $lng, $zoom = 15, $mapType = 'roadmap')
    {
        $apiKey = 'AIzaSyBwjrCx-T4UlUDmXALumCOWkJv-B7m9yCE';
        
        return "https://www.google.com/maps/embed/v1/place"
            . "?key={$apiKey}"
            . "&q={$lat},{$lng}"
            . "&zoom={$zoom}"
            . "&maptype={$mapType}"
            . "&language=en"
            . "&region=NP";
    }
    
    /**
     * Generate a Google Maps Static API URL for a location
     *
     * @param float $lat Latitude
     * @param float $lng Longitude
     * @param int $zoom Zoom level (1-20)
     * @param int $width Image width in pixels
     * @param int $height Image height in pixels
     * @param string $mapType Map type (roadmap, satellite, hybrid, terrain)
     * @return string The Google Maps Static API URL
     */
    public static function getStaticMapUrl($lat, $lng, $zoom = 15, $width = 600, $height = 400, $mapType = 'roadmap')
    {
        $apiKey = 'AIzaSyBwjrCx-T4UlUDmXALumCOWkJv-B7m9yCE';
        
        return "https://maps.googleapis.com/maps/api/staticmap"
            . "?center={$lat},{$lng}"
            . "&zoom={$zoom}"
            . "&size={$width}x{$height}"
            . "&maptype={$mapType}"
            . "&markers=color:red%7C{$lat},{$lng}"
            . "&key={$apiKey}";
    }
    
    /**
     * Generate an OpenStreetMap URL for a location
     *
     * @param float $lat Latitude
     * @param float $lng Longitude
     * @param int $zoom Zoom level (1-19)
     * @return string The OpenStreetMap URL
     */
    public static function getOpenStreetMapUrl($lat, $lng, $zoom = 15)
    {
        // Calculate the bounding box (approximately 0.01 degrees around the point)
        $minLng = $lng - 0.01;
        $minLat = $lat - 0.01;
        $maxLng = $lng + 0.01;
        $maxLat = $lat + 0.01;
        
        return "https://www.openstreetmap.org/export/embed.html"
            . "?bbox={$minLng}%2C{$minLat}%2C{$maxLng}%2C{$maxLat}"
            . "&layer=mapnik"
            . "&marker={$lat}%2C{$lng}";
    }
}
