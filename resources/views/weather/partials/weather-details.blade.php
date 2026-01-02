<div class="mb-6">
    <h2 class="text-3xl font-bold mb-2">{{ $cityName }}</h2>
    <p class="text-gray-600">{{ now()->translatedFormat('l, d F Y') }}</p>
</div>

@php
    $current = $weather['current'];
    $weatherCode = $current['weather_code'];
    $weatherDesc = \App\Services\WeatherService::getWeatherDescription($weatherCode);
    $tempUnit = $weather['current_units']['temperature_2m'] ?? '°C';
    $windUnit = $weather['current_units']['wind_speed_10m'] ?? 'km/h';
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="text-center bg-gradient-to-br from-blue-50 to-purple-50 p-6 rounded-xl">
        <div class="text-6xl mb-3">{{ $weatherDesc['icon'] }}</div>
        <div class="text-5xl font-bold mb-2">{{ round($current['temperature_2m']) }}{{ $tempUnit }}</div>
        <div class="text-xl text-gray-700 font-semibold">{{ $weatherDesc['desc'] }}</div>
        <div class="text-gray-600 mt-2">Terasa seperti {{ round($current['apparent_temperature']) }}{{ $tempUnit }}</div>
    </div>
    
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-blue-50 p-4 rounded-xl hover:shadow-md transition">
            <div class="text-sm text-gray-600 mb-1">💧 Kelembaban</div>
            <div class="text-2xl font-bold text-blue-800">{{ $current['relative_humidity_2m'] }}%</div>
        </div>
        <div class="bg-green-50 p-4 rounded-xl hover:shadow-md transition">
            <div class="text-sm text-gray-600 mb-1">💨 Kec. Angin</div>
            <div class="text-2xl font-bold text-green-800">{{ round($current['wind_speed_10m']) }} {{ $windUnit }}</div>
        </div>
        <div class="bg-purple-50 p-4 rounded-xl hover:shadow-md transition">
            <div class="text-sm text-gray-600 mb-1">🌡️ Tekanan</div>
            <div class="text-2xl font-bold text-purple-800">{{ round($current['pressure_msl']) }} hPa</div>
        </div>
        <div class="bg-orange-50 p-4 rounded-xl hover:shadow-md transition">
            <div class="text-sm text-gray-600 mb-1">🌧️ Hujan</div>
            <div class="text-2xl font-bold text-orange-800">{{ $current['precipitation'] }} mm</div>
        </div>
    </div>
</div>

<!-- Hourly Forecast -->
<div class="bg-gradient-to-r from-blue-50 to-purple-50 p-6 rounded-xl mb-6">
    <h3 class="text-xl font-bold mb-4">Prakiraan Per Jam (24 Jam)</h3>
    <div class="grid grid-cols-4 md:grid-cols-8 lg:grid-cols-12 gap-2 overflow-x-auto">
        @foreach(array_slice($weather['hourly']['time'], 0, 24) as $index => $time)
            @php
                $hour = \Carbon\Carbon::parse($time)->format('H:i');
                $temp = round($weather['hourly']['temperature_2m'][$index]);
                $hourWeatherCode = $weather['hourly']['weather_code'][$index];
                $hourWeather = \App\Services\WeatherService::getWeatherDescription($hourWeatherCode);
            @endphp
            <div class="bg-white p-3 rounded-lg text-center hover:shadow-md transition">
                <div class="text-xs font-semibold text-gray-600">{{ $hour }}</div>
                <div class="text-2xl my-1">{{ $hourWeather['icon'] }}</div>
                <div class="text-sm font-bold">{{ $temp }}°</div>
            </div>
        @endforeach
    </div>
</div>

<!-- Daily Forecast -->
<div class="bg-gradient-to-r from-green-50 to-blue-50 p-6 rounded-xl">
    <h3 class="text-xl font-bold mb-4">Prakiraan 7 Hari</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
        @foreach($weather['daily']['time'] as $index => $date)
            @php
                $dayName = \Carbon\Carbon::parse($date)->translatedFormat('D');
                $dayWeatherCode = $weather['daily']['weather_code'][$index];
                $dayWeather = \App\Services\WeatherService::getWeatherDescription($dayWeatherCode);
                $maxTemp = round($weather['daily']['temperature_2m_max'][$index]);
                $minTemp = round($weather['daily']['temperature_2m_min'][$index]);
            @endphp
            <div class="bg-white p-4 rounded-lg text-center hover:shadow-md transition">
                <div class="font-semibold text-sm text-gray-700">{{ $dayName }}</div>
                <div class="text-3xl my-2">{{ $dayWeather['icon'] }}</div>
                <div class="text-sm font-bold text-gray-800">{{ $maxTemp }}°</div>
                <div class="text-xs text-gray-600">{{ $minTemp }}°</div>
            </div>
        @endforeach
    </div>
</div>
