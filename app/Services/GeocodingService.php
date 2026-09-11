<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeocodingService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GOOGLE_MAPS_API_KEY');
    }

    /**
     * Get geocoding data for a given address.
     */
    public function geocodeAddress(string $address): array
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $address,
            'key' => $this->apiKey,
        ]);

        return $response->json();
    }

    /**
     * Get address details for given latitude and longitude.
     */
    public function reverseGeocode(float $latitude, float $longitude): array
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'latlng' => "{$latitude},{$longitude}",
            'key' => $this->apiKey,
        ]);

        return $response->json();
    }
}
