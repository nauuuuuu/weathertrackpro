<?php

namespace App\Http\Controllers;

use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    /**
     * Display the weather dashboard
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $latitude = $request->get('lat');
        $longitude = $request->get('lon');
        $cityName = $request->get('city');

        // Save search history if user is authenticated and location is provided
        if (auth()->check() && $latitude && $longitude && $cityName) {
            try {
                $this->saveSearchHistory(
                    $cityName,
                    (float) $latitude,
                    (float) $longitude
                );
            } catch (\Exception $e) {
                Log::error('Failed to save search history', [
                    'error' => $e->getMessage(),
                    'city' => $cityName
                ]);
            }
        }

        return view('weather.index');
    }

    /**
     * Save search history to database
     *
     * @param string $cityName
     * @param float $latitude
     * @param float $longitude
     * @param string|null $country
     * @return void
     */
    private function saveSearchHistory(
        string $cityName,
        float $latitude,
        float $longitude,
        ?string $country = null
    ): void {
        if (!auth()->check()) {
            return;
        }

        try {
            // Check if already exists in recent history (last 5 minutes)
            $recentSearch = SearchHistory::where('user_id', auth()->id())
                ->where('city_name', $cityName)
                ->where('latitude', $latitude)
                ->where('longitude', $longitude)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->exists();

            if (!$recentSearch) {
                SearchHistory::create([
                    'user_id' => auth()->id(),
                    'city_name' => $cityName,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'country' => $country,
                    'ip_address' => request()->ip(),
                ]);

                Log::info('Search history saved', [
                    'user_id' => auth()->id(),
                    'city' => $cityName
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error saving search history', [
                'error' => $e->getMessage(),
                'city' => $cityName
            ]);
        }
    }
}
