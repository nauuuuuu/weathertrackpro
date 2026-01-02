@extends('layouts.app')

@section('title', 'Preferensi')

@section('content')
<div class="py-12 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="weather-card p-8">
            <h1 class="text-3xl font-bold mb-6">Preferensi Pengguna</h1>
            
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('preferences.update') }}">
                @csrf
                @method('PATCH')
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Satuan Suhu</label>
                    <select name="temperature_unit" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="celsius" {{ $preference->temperature_unit === 'celsius' ? 'selected' : '' }}>Celsius (°C)</option>
                        <option value="fahrenheit" {{ $preference->temperature_unit === 'fahrenheit' ? 'selected' : '' }}>Fahrenheit (°F)</option>
                    </select>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Satuan Kecepatan Angin</label>
                    <select name="wind_speed_unit" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="kmh" {{ $preference->wind_speed_unit === 'kmh' ? 'selected' : '' }}>Kilometer per Jam (km/h)</option>
                        <option value="ms" {{ $preference->wind_speed_unit === 'ms' ? 'selected' : '' }}>Meter per Detik (m/s)</option>
                        <option value="mph" {{ $preference->wind_speed_unit === 'mph' ? 'selected' : '' }}>Miles per Hour (mph)</option>
                    </select>
                </div>
                
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="auto_location" value="1" {{ $preference->auto_location ? 'checked' : '' }} 
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Gunakan lokasi otomatis saat membuka aplikasi</span>
                    </label>
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                        Simpan Preferensi
                    </button>
                    <a href="{{ route('home') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
