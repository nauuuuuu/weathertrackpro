<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FavoriteCity;
use App\Models\SearchHistory;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // HAPUS __construct() yang lama
    
    public function index()
    {
        // Cek manual di sini atau pakai route middleware
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $totalUsers = User::where('role', 'user')->count();
        $activeUsers = User::where('role', 'user')->where('is_active', true)->count();
        $totalSearches = SearchHistory::count();
        $totalFavorites = FavoriteCity::count();

        $topCities = SearchHistory::select('city_name', DB::raw('count(*) as total'))
            ->groupBy('city_name')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $recentUsers = User::where('role', 'user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'totalSearches',
            'totalFavorites',
            'topCities',
            'recentUsers'
        ));
    }
}
