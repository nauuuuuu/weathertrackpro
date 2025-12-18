@extends('layouts.app')

@section('title', 'Edit Profil')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center text-white hover:underline">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 mb-6">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">👤 Edit Profil</h2>
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-indigo-700 transition">
                    💾 Simpan Perubahan
                </button>
            </form>
        </div>

        <!-- Preferences -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">⚙️ Preferensi Cuaca</h2>
            
            <form method="POST" action="{{ route('profile.preferences.update') }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Satuan Suhu</label>
                        <select name="temperature_unit" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="celsius" {{ optional($user->preference)->temperature_unit == 'celsius' ? 'selected' : '' }}>Celsius (°C)</option>
                            <option value="fahrenheit" {{ optional($user->preference)->temperature_unit == 'fahrenheit' ? 'selected' : '' }}>Fahrenheit (°F)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Satuan Kecepatan Angin</label>
                        <select name="wind_speed_unit" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="kmh" {{ optional($user->preference)->wind_speed_unit == 'kmh' ? 'selected' : '' }}>km/h</option>
                            <option value="ms" {{ optional($user->preference)->wind_speed_unit == 'ms' ? 'selected' : '' }}>m/s</option>
                            <option value="mph" {{ optional($user->preference)->wind_speed_unit == 'mph' ? 'selected' : '' }}>mph</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="auto_location" value="1" {{ optional($user->preference)->auto_location ? 'checked' : '' }}
                            class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-2 text-gray-700">Aktifkan deteksi lokasi otomatis</span>
                    </label>
                </div>

                <button type="submit" class="w-full mt-6 bg-gradient-to-r from-green-600 to-teal-600 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-teal-700 transition">
                    ⚙️ Update Preferensi
                </button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">🔐 Ubah Password</h2>
            
            <form method="POST" action="{{ route('profile.password.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Password Lama</label>
                    <input type="password" name="current_password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('current_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Password Baru</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-pink-600 text-white py-3 rounded-lg font-semibold hover:from-red-700 hover:to-pink-700 transition">
                    🔒 Ubah Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
