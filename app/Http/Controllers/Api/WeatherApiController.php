<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherApiController extends Controller
{
    /**
     * Get current weather data from Open-Meteo API
     */
    public function getCurrentWeather(Request $request): JsonResponse
    {
        try {
            // Validate input
            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            $latitude = $validated['latitude'];
            $longitude = $validated['longitude'];

            Log::info('Weather API Request', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'ip' => $request->ip()
            ]);

            // Get user preferences if authenticated
            $tempUnit = 'celsius';
            $windUnit = 'kmh';

            if (auth()->check() && auth()->user()->preference) {
                $tempUnit = auth()->user()->preference->temperature_unit ?? 'celsius';
                $windUnit = auth()->user()->preference->wind_speed_unit ?? 'kmh';
            }

            // Build Open-Meteo API request
            $apiUrl = 'https://api.open-meteo.com/v1/forecast';
            
            $params = [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,weather_code,pressure_msl,wind_speed_10m',
                'hourly' => 'temperature_2m,weather_code',
                'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,sunrise,sunset,precipitation_probability_max',
                'timezone' => 'auto',
                'temperature_unit' => $tempUnit,
                'wind_speed_unit' => $windUnit,
            ];

            Log::info('Calling Open-Meteo API', ['url' => $apiUrl, 'params' => $params]);

            // Make HTTP request to Open-Meteo
            $response = Http::timeout(20)
                ->retry(3, 100)
                ->get($apiUrl, $params);

            // Check response status
            if ($response->failed()) {
                Log::error('Open-Meteo API failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data cuaca dari Open-Meteo API',
                    'error' => $response->body()
                ], 500);
            }

            $weatherData = $response->json();
            
            Log::info('Weather data fetched successfully', [
                'has_current' => isset($weatherData['current']),
                'has_daily' => isset($weatherData['daily']),
                'has_hourly' => isset($weatherData['hourly'])
            ]);

            return response()->json($weatherData);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error', ['errors' => $e->errors()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Data koordinat tidak valid',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Weather API Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search cities by name using Geocoding API
     */
    public function searchCities(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'query' => 'required|string|min:2',
            ]);

            $response = Http::timeout(10)
                ->get('https://geocoding-api.open-meteo.com/v1/search', [
                    'name' => $validated['query'],
                    'count' => 10,
                    'language' => 'id',
                    'format' => 'json'
                ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari kota'
            ], 500);

        } catch (\Exception $e) {
            Log::error('City search error', ['message' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari kota'
            ], 500);
        }
    }
}
