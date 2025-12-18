<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

class GeocodingService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.geocoding.url', 'https://geocoding-api.open-meteo.com/v1');
    }

    public function searchCities(string $query, int $count = 10): array
    {
        try {
            /** @var Response $response */
            $response = Http::timeout(10)->get("{$this->baseUrl}/search", [
                'name' => $query,
                'count' => $count,
                'language' => 'id',
                'format' => 'json',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['results'] ?? [];
            }

            Log::error('Geocoding API error', [
                'status' => $response->status(),
                'query' => $query
            ]);
            
            return [];
            
        } catch (\Exception $e) {
            Log::error('Geocoding exception', [
                'message' => $e->getMessage(),
                'query' => $query
            ]);
            return [];
        }
    }

    public function reverseGeocode(float $latitude, float $longitude): ?array
    {
        try {
            /** @var Response $response */
            $response = Http::timeout(10)->get("{$this->baseUrl}/search", [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'count' => 1,
                'language' => 'id',
                'format' => 'json',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['results'][0] ?? null;
            }

            return null;
            
        } catch (\Exception $e) {
            Log::error('Reverse geocoding exception', [
                'message' => $e->getMessage(),
                'lat' => $latitude,
                'lon' => $longitude
            ]);
            return null;
        }
    }
}
