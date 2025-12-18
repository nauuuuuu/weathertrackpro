@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
<div class="py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="weather-card p-8 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $user->name }}</h1>
                    <p class="text-gray-600">{{ $user->email }}</p>
                    <span class="inline-block mt-2 px-4 py-1 rounded-full text-sm font-bold {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $user->is_active ? '✅ Aktif' : '❌ Nonaktif' }}
                    </span>
                </div>
                <a href="{{ route('admin.users.index') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                    ← Kembali
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-lg">
                    <div class="text-sm text-gray-600 mb-1">Status</div>
                    <div class="text-2xl font-bold text-blue-800">{{ $user->is_active ? '✅ Aktif' : '❌ Nonaktif' }}</div>
                </div>
                <div class="bg-gradient-to-r from-green-50 to-green-100 p-6 rounded-lg">
                    <div class="text-sm text-gray-600 mb-1">Terdaftar Sejak</div>
                    <div class="text-2xl font-bold text-green-800">{{ $user->created_at->format('d M Y') }}</div>
                </div>
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-6 rounded-lg">
                    <div class="text-sm text-gray-600 mb-1">Kota Favorit</div>
                    <div class="text-2xl font-bold text-purple-800">{{ $user->favoriteCities->count() }}</div>
                </div>
                <div class="bg-gradient-to-r from-orange-50 to-orange-100 p-6 rounded-lg">
                    <div class="text-sm text-gray-600 mb-1">Total Pencarian</div>
                    <div class="text-2xl font-bold text-orange-800">{{ $user->searchHistories->count() }}</div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="weather-card p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">⭐ Kota Favorit ({{ $user->favoriteCities->count() }})</h2>
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @forelse($user->favoriteCities as $city)
                        <div class="p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg hover:shadow-md transition">
                            <div class="font-semibold text-gray-800">{{ $city->city_name }}</div>
                            <div class="text-sm text-gray-600">{{ $city->country }} • Ditambahkan {{ $city->created_at->diffForHumans() }}</div>
                            <div class="text-xs text-gray-500 mt-1">Lat: {{ $city->latitude }}, Lon: {{ $city->longitude }}</div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-2">📍</div>
                            <p>Belum ada kota favorit</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <div class="weather-card p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">🕐 Riwayat Pencarian (20 Terakhir)</h2>
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @forelse($user->searchHistories as $search)
                        <div class="p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg hover:shadow-md transition">
                            <div class="font-semibold text-gray-800">{{ $search->city_name }}</div>
                            <div class="text-xs text-gray-500 flex justify-between mt-1">
                                <span>{{ $search->created_at->format('d M Y H:i') }}</span>
                                <span>IP: {{ $search->ip_address }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-2">🔍</div>
                            <p>Belum ada riwayat pencarian</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
