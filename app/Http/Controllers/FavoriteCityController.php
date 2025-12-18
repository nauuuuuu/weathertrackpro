<?php

namespace App\Http\Controllers;

use App\Models\FavoriteCity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;

class FavoriteCityController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, string>
     */
    public static function middleware(): array
    {
        return ['auth'];
    }

    /**
     * Display favorite cities
     *
     * @return View
     */
    public function index(): View
    {
        $favorites = auth()->user()->favoriteCities()
            ->orderBy('order')
            ->get();

        return view('favorites.index', compact('favorites'));
    }

    /**
     * Store a new favorite city
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'city_name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'country' => 'nullable|string|max:100',
        ]);

        // Check if already exists
        $exists = auth()->user()->favoriteCities()
            ->where('latitude', $validated['latitude'])
            ->where('longitude', $validated['longitude'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Kota ini sudah ada di favorit Anda'
            ], 422);
        }

        // Get next order number
        $maxOrder = auth()->user()->favoriteCities()->max('order') ?? 0;

        $favorite = auth()->user()->favoriteCities()->create([
            'city_name' => $validated['city_name'],
            'country' => $validated['country'] ?? null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'order' => $maxOrder + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => '⭐ Kota berhasil ditambahkan ke favorit!',
            'data' => $favorite
        ]);
    }

    /**
     * Remove from favorites
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $deleted = auth()->user()->favoriteCities()
            ->where('latitude', $validated['latitude'])
            ->where('longitude', $validated['longitude'])
            ->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => '🗑️ Kota berhasil dihapus dari favorit'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kota tidak ditemukan di favorit'
        ], 404);
    }

    /**
     * Check if city is favorited
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkFavorite(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $isFavorite = auth()->user()->favoriteCities()
            ->where('latitude', $validated['latitude'])
            ->where('longitude', $validated['longitude'])
            ->exists();

        return response()->json([
            'is_favorite' => $isFavorite
        ]);
    }

    /**
     * Update favorite order
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'favorites' => 'required|array',
            'favorites.*.id' => 'required|exists:favorite_cities,id',
            'favorites.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['favorites'] as $favoriteData) {
            auth()->user()->favoriteCities()
                ->where('id', $favoriteData['id'])
                ->update(['order' => $favoriteData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan favorit berhasil diupdate'
        ]);
    }

    /**
     * Delete specific favorite by ID
     *
     * @param FavoriteCity $favorite
     * @return RedirectResponse
     */
    public function destroyById(FavoriteCity $favorite): RedirectResponse
    {
        // Make sure the favorite belongs to the authenticated user
        if ($favorite->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $favorite->delete();

        return redirect()->route('favorites.index')
            ->with('success', '🗑️ Kota berhasil dihapus dari favorit');
    }
}
