<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

class WeatherService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.weather.url', 'https://api.open-meteo.com/v1');
    }

    public function getCurrentWeather(
        float $latitude,
        float $longitude,
        string $temperatureUnit = 'celsius',
        string $windSpeedUnit = 'kmh'
    ): ?array {
        try {
            /** @var Response $response */
            $response = Http::timeout(15)->get("{$this->baseUrl}/forecast", [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => implode(',', [
                    'temperature_2m',
                    'relative_humidity_2m',
                    'apparent_temperature',
                    'is_day',
                    'precipitation',
                    'rain',
                    'showers',
                    'snowfall',
                    'weather_code',
                    'cloud_cover',
                    'pressure_msl',
                    'surface_pressure',
                    'wind_speed_10m',
                    'wind_direction_10m',
                    'wind_gusts_10m',
                ]),
                'hourly' => implode(',', [
                    'temperature_2m',
                    'relative_humidity_2m',
                    'precipitation_probability',
                    'precipitation',
                    'weather_code',
                    'wind_speed_10m',
                ]),
                'daily' => implode(',', [
                    'weather_code',
                    'temperature_2m_max',
                    'temperature_2m_min',
                    'precipitation_sum',
                    'rain_sum',
                    'precipitation_probability_max',
                    'wind_speed_10m_max',
                    'wind_gusts_10m_max',
                    'sunrise',
                    'sunset',
                ]),
                'temperature_unit' => $temperatureUnit,
                'wind_speed_unit' => $windSpeedUnit,
                'precipitation_unit' => 'mm',
                'timezone' => 'auto',
                'forecast_days' => 7,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Weather API error', [
                'status' => $response->status(),
                'lat' => $latitude,
                'lon' => $longitude
            ]);
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Weather API exception', [
                'message' => $e->getMessage(),
                'lat' => $latitude,
                'lon' => $longitude
            ]);
            return null;
        }
    }

    // UBAH JADI STATIC
    public static function getWeatherDescription(int $code): array
    {
        $descriptions = [
            0 => ['desc' => 'Cerah', 'icon' => '☀️'],
            1 => ['desc' => 'Cerah Sebagian', 'icon' => '🌤️'],
            2 => ['desc' => 'Berawan Sebagian', 'icon' => '⛅'],
            3 => ['desc' => 'Berawan', 'icon' => '☁️'],
            45 => ['desc' => 'Berkabut', 'icon' => '🌫️'],
            48 => ['desc' => 'Kabut Beku', 'icon' => '🌫️'],
            51 => ['desc' => 'Gerimis Ringan', 'icon' => '🌦️'],
            53 => ['desc' => 'Gerimis Sedang', 'icon' => '🌦️'],
            55 => ['desc' => 'Gerimis Lebat', 'icon' => '🌧️'],
            61 => ['desc' => 'Hujan Ringan', 'icon' => '🌧️'],
            63 => ['desc' => 'Hujan Sedang', 'icon' => '🌧️'],
            65 => ['desc' => 'Hujan Lebat', 'icon' => '⛈️'],
            71 => ['desc' => 'Salju Ringan', 'icon' => '🌨️'],
            73 => ['desc' => 'Salju Sedang', 'icon' => '🌨️'],
            75 => ['desc' => 'Salju Lebat', 'icon' => '❄️'],
            80 => ['desc' => 'Hujan Rintik', 'icon' => '🌦️'],
            81 => ['desc' => 'Hujan Deras', 'icon' => '⛈️'],
            82 => ['desc' => 'Hujan Sangat Deras', 'icon' => '⛈️'],
            95 => ['desc' => 'Petir', 'icon' => '⚡'],
            96 => ['desc' => 'Petir dengan Hujan Es', 'icon' => '⛈️'],
            99 => ['desc' => 'Petir dengan Hujan Es Lebat', 'icon' => '⛈️'],
        ];

        return $descriptions[$code] ?? ['desc' => 'Tidak Diketahui', 'icon' => '❓'];
    }
}
