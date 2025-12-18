@extends('layouts.app')

@section('title', 'Kota Favorit')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
    
    .favorite-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s;
    }
    
    .favorite-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
        background: rgba(255, 255, 255, 0.25);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">⭐ Kota Favorit</h1>
                <p class="text-white opacity-80">Akses cepat ke kota-kota yang sering Anda pantau</p>
            </div>
            <a href="{{ route('home') }}" class="bg-white/20 backdrop-blur-lg text-white px-6 py-3 rounded-xl hover:bg-white/30 transition font-semibold">
                ← Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500 text-white rounded-xl shadow-lg">
                {{ session('success') }}
            </div>
        @endif

        @if($favorites->isEmpty())
            <div class="text-center py-20">
                <div class="text-8xl mb-4">📍</div>
                <h2 class="text-2xl font-bold text-white mb-2">Belum Ada Kota Favorit</h2>
                <p class="text-white opacity-80 mb-6">Mulai tambahkan kota favorit dari halaman cuaca</p>
                <a href="{{ route('home') }}" class="inline-block bg-white text-purple-600 px-8 py-3 rounded-xl font-bold hover:bg-gray-100 transition">
                    🔍 Cari Kota
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($favorites as $favorite)
                    <div class="favorite-card p-6 text-white">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold mb-1">{{ $favorite->city_name }}</h3>
                                <p class="opacity-80 text-sm">{{ $favorite->country }}</p>
                            </div>
                            <form method="POST" action="{{ route('favorites.destroy', $favorite) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus dari favorit?')" 
                                    class="text-red-400 hover:text-red-300 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        
                        <div class="flex items-center gap-2 text-sm opacity-80 mb-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>{{ number_format($favorite->latitude, 4) }}, {{ number_format($favorite->longitude, 4) }}</span>
                        </div>
                        
                        <a href="/?lat={{ $favorite->latitude }}&lon={{ $favorite->longitude }}&city={{ urlencode($favorite->city_name) }}" 
                            class="block w-full bg-white/20 hover:bg-white/30 text-center py-3 rounded-lg font-semibold transition">
                            🌤️ Lihat Cuaca
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
