@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">🔐 Admin Dashboard</h1>
            <p class="text-white text-opacity-90">Kelola sistem WeatherTrackPro</p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="weather-card p-6 hover:shadow-xl transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Users</p>
                        <p class="text-4xl font-bold text-blue-600 mt-2">{{ $totalUsers }}</p>
                    </div>
                    <div class="text-5xl">👥</div>
                </div>
            </div>
            
            <div class="weather-card p-6 hover:shadow-xl transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Users Aktif</p>
                        <p class="text-4xl font-bold text-green-600 mt-2">{{ $activeUsers }}</p>
                    </div>
                    <div class="text-5xl">✅</div>
                </div>
            </div>
            
            <div class="weather-card p-6 hover:shadow-xl transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Pencarian</p>
                        <p class="text-4xl font-bold text-purple-600 mt-2">{{ $totalSearches }}</p>
                    </div>
                    <div class="text-5xl">🔍</div>
                </div>
            </div>
            
            <div class="weather-card p-6 hover:shadow-xl transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Kota Favorit</p>
                        <p class="text-4xl font-bold text-orange-600 mt-2">{{ $totalFavorites }}</p>
                    </div>
                    <div class="text-5xl">⭐</div>
                </div>
            </div>
        </div>
        
        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Top Cities -->
            <div class="weather-card p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">📊 Kota Paling Sering Dipantau</h2>
                    <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                        Lihat Semua →
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($topCities as $index => $city)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg hover:shadow-md transition">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full font-bold text-sm">
                                    {{ $index + 1 }}
                                </span>
                                <span class="font-semibold text-gray-800">{{ $city->city_name }}</span>
                            </div>
                            <span class="bg-blue-600 text-white px-4 py-1 rounded-full text-sm font-bold">
                                {{ $city->total }}x
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-2">📭</div>
                            <p>Belum ada data pencarian</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Recent Users -->
            <div class="weather-card p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">🆕 User Terbaru</h2>
                    <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                        Lihat Semua →
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($recentUsers as $user)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg hover:shadow-md transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-600">{{ $user->email }}</div>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 text-right">
                                <div>{{ $user->created_at->format('d M Y') }}</div>
                                <div>{{ $user->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-2">👤</div>
                            <p>Belum ada user terdaftar</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="weather-card p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">⚡ Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-4 p-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:shadow-xl transition transform hover:-translate-y-1">
                    <div class="text-4xl">👥</div>
                    <div>
                        <div class="font-bold text-lg">Kelola Users</div>
                        <div class="text-sm opacity-90">Lihat & kelola semua user</div>
                    </div>
                </a>
                
                <a href="{{ route('home') }}" class="flex items-center gap-4 p-4 bg-gradient-to-r from-green-500 to-teal-600 text-white rounded-lg hover:shadow-xl transition transform hover:-translate-y-1">
                    <div class="text-4xl">🌤️</div>
                    <div>
                        <div class="font-bold text-lg">Lihat Dashboard</div>
                        <div class="text-sm opacity-90">Kembali ke halaman utama</div>
                    </div>
                </a>
                
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-4 p-4 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-lg hover:shadow-xl transition transform hover:-translate-y-1">
                    <div class="text-4xl">⚙️</div>
                    <div>
                        <div class="font-bold text-lg">Profile Admin</div>
                        <div class="text-sm opacity-90">Edit profile & password</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
