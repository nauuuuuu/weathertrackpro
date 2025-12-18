@extends('layouts.app')

@section('title', 'WeatherTrackPro - Real-time Weather')

@push('styles')
<style>
    body { margin: 0; padding: 0; overflow-x: hidden; }

    .weather-gradient {
        background: linear-gradient(180deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        min-height: 100vh;
        position: relative;
        overflow: hidden;
    }

    .weather-gradient.morning { background: linear-gradient(180deg, #89f7fe 0%, #66a6ff 100%); }
    .weather-gradient.day { background: linear-gradient(180deg, #4facfe 0%, #00f2fe 100%); }
    .weather-gradient.evening { background: linear-gradient(180deg, #fa709a 0%, #fee140 100%); }
    .weather-gradient.night { background: linear-gradient(180deg, #0f2027 0%, #203a43 50%, #2c5364 100%); }

    .glass-effect {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .weather-main-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.05) 100%);
        backdrop-filter: blur(30px);
        border-radius: 32px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .detail-card {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 16px;
        transition: all 0.3s;
    }

    .detail-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    /* Map Responsive Fix */
    #map { 
        height: 500px; 
        width: 100%;
        border-radius: 24px; 
        overflow: hidden;
        position: relative;
        z-index: 1;
    }
    
    @media (max-width: 768px) {
        #map { 
            height: 350px; 
        }
    }

    .hourly-scroll {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding: 16px 0;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
    }

    .hourly-scroll::-webkit-scrollbar { height: 6px; }
    .hourly-scroll::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.1); border-radius: 3px; }
    .hourly-scroll::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.3); border-radius: 3px; }

    .loading-spinner {
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top: 4px solid white;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .fade-in { animation: fadeIn 0.5s ease-in; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .favorite-btn {
        transition: all 0.3s;
    }
    
    .favorite-btn:hover {
        transform: scale(1.1);
    }
    
    .favorite-btn.active {
        color: #fbbf24;
        transform: scale(1.2);
    }
    
    /* Custom Leaflet Control Style */
    .leaflet-control-layers {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        padding: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .leaflet-control-layers-toggle {
        background-image: none;
        width: auto;
        height: auto;
    }
    
    .custom-marker {
        background: transparent;
        border: none;
    }
</style>
@endpush

@section('content')
<div id="weatherApp" class="weather-gradient night">
    <div class="container mx-auto px-4 py-6">
        
        <!-- Header Simple -->
        <div class="flex items-center justify-between mb-6 text-white fade-in">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="text-white hover:bg-white/20 p-3 rounded-lg transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div>
                    <h1 id="cityName" class="text-2xl font-bold">WeatherTrackPro</h1>
                    <p id="currentTime" class="text-sm opacity-80"></p>
                </div>
            </div>
            
            @auth
            <button id="favoriteBtn" onclick="toggleFavorite()" class="favorite-btn text-white hover:bg-white/20 p-3 rounded-lg transition text-2xl" title="Tambah ke Favorit">
                ☆
            </button>
            @else
            <a href="{{ route('login') }}" class="text-white hover:bg-white/20 p-3 rounded-lg transition" title="Login untuk favorit">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
            </a>
            @endauth
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="text-center py-20">
            <div class="loading-spinner mx-auto mb-4"></div>
            <p class="text-white text-lg">Memuat data cuaca...</p>
            <p class="text-white text-sm opacity-70 mt-2" id="loadingStatus">Mendeteksi lokasi Anda...</p>
        </div>

        <!-- Main Weather Card -->
        <div id="weatherContent" style="display: none;">
            <div class="weather-main-card p-8 mb-6 text-white text-center fade-in">
                <div class="mb-4">
                    <div id="weatherIcon" class="text-9xl mb-4">🌤️</div>
                    <div id="temperature" class="text-7xl font-bold mb-2">--°</div>
                    <div id="weatherDescription" class="text-2xl mb-2">--</div>
                    <div id="feelsLike" class="text-lg opacity-80">Terasa seperti --°</div>
                </div>
                <div class="flex justify-center gap-8 mt-6">
                    <div>
                        <div class="text-3xl font-bold" id="tempMax">--°</div>
                        <div class="text-sm opacity-80">Maks</div>
                    </div>
                    <div class="w-px bg-white/30"></div>
                    <div>
                        <div class="text-3xl font-bold" id="tempMin">--°</div>
                        <div class="text-sm opacity-80">Min</div>
                    </div>
                </div>
            </div>

            <!-- Hourly Forecast -->
            <div class="glass-effect p-6 mb-6 fade-in">
                <h3 class="text-white text-lg font-bold mb-4">📊 Prakiraan Per Jam</h3>
                <div id="hourlyForecast" class="hourly-scroll"></div>
            </div>

            <!-- Weather Details Grid -->
            <div class="grid grid-cols-2 gap-4 mb-6 fade-in">
                <div class="detail-card text-white">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-2xl">💧</span>
                        <span class="text-sm opacity-80">Kelembaban</span>
                    </div>
                    <div id="humidity" class="text-3xl font-bold">--%</div>
                </div>
                <div class="detail-card text-white">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-2xl">💨</span>
                        <span class="text-sm opacity-80">Angin</span>
                    </div>
                    <div id="windSpeed" class="text-3xl font-bold">-- km/h</div>
                </div>
                <div class="detail-card text-white">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-2xl">🌡️</span>
                        <span class="text-sm opacity-80">Tekanan</span>
                    </div>
                    <div id="pressure" class="text-3xl font-bold">-- mb</div>
                </div>
                <div class="detail-card text-white">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-2xl">🌧️</span>
                        <span class="text-sm opacity-80">Curah Hujan</span>
                    </div>
                    <div id="precipitation" class="text-3xl font-bold">-- mm</div>
                </div>
            </div>

            <!-- Sunrise & Sunset -->
            <div class="glass-effect p-6 mb-6 fade-in">
                <h3 class="text-white text-lg font-bold mb-4">☀️ Matahari</h3>
                <div class="flex justify-between items-center text-white">
                    <div class="text-center">
                        <div class="text-3xl mb-2">🌅</div>
                        <div class="text-sm opacity-80">Terbit</div>
                        <div id="sunrise" class="text-xl font-bold">--:--</div>
                    </div>
                    <div class="flex-1 mx-4">
                        <div class="h-1 bg-white/20 rounded-full overflow-hidden">
                            <div id="sunProgress" class="h-full bg-yellow-400 rounded-full transition-all" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl mb-2">🌇</div>
                        <div class="text-sm opacity-80">Terbenam</div>
                        <div id="sunset" class="text-xl font-bold">--:--</div>
                    </div>
                </div>
            </div>

            <!-- Weather Radar Map -->
            <div class="glass-effect p-6 mb-6 fade-in">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-white text-lg font-bold">🗺️ Radar Cuaca Real-time</h3>
                    <div class="flex gap-2">
                        <button id="radarToggle" onclick="toggleRadarLayer()" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                            🌧️ Radar: ON
                        </button>
                    </div>
                </div>
                <div id="map"></div>
                <div class="mt-3 flex items-center justify-between text-white text-xs opacity-70">
                    <span>© OpenStreetMap | Weather Radar © Windy</span>
                    <span id="radarTimestamp">Loading radar...</span>
                </div>
            </div>

            <!-- Weekly Forecast -->
            <div class="glass-effect p-6 mb-20 fade-in">
                <h3 class="text-white text-lg font-bold mb-4">📅 Prakiraan 7 Hari</h3>
                <div id="weeklyForecast" class="space-y-3"></div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
let map = null;
let marker = null;
let radarLayer = null;
let cloudLayer = null;
let currentLat = null;
let currentLon = null;
let currentCity = null;
let isFavorite = false;
let radarEnabled = true;

// Update time
function updateTime() {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    document.getElementById('currentTime').textContent = now.toLocaleDateString('id-ID', options);
}

// Update theme
function updateTheme() {
    const hour = new Date().getHours();
    const app = document.getElementById('weatherApp');
    app.classList.remove('morning', 'day', 'evening', 'night');
    
    if (hour >= 5 && hour < 10) app.classList.add('morning');
    else if (hour >= 10 && hour < 17) app.classList.add('day');
    else if (hour >= 17 && hour < 20) app.classList.add('evening');
    else app.classList.add('night');
}

// Update loading status
function updateLoadingStatus(message) {
    const statusEl = document.getElementById('loadingStatus');
    if (statusEl) statusEl.textContent = message;
}

// Show error
function showError(title, message) {
    document.getElementById('loadingState').innerHTML = `
        <div class="text-center py-20">
            <div class="text-6xl mb-4">❌</div>
            <p class="text-white text-xl font-bold mb-2">${title}</p>
            <p class="text-white opacity-80 mb-4">${message}</p>
            <button onclick="location.reload()" class="bg-white text-purple-600 px-6 py-3 rounded-lg font-bold hover:bg-gray-100 transition">
                🔄 Coba Lagi
            </button>
        </div>
    `;
}

// Initialize map with Windy radar (BEST FREE OPTION)
function initMap(lat, lon) {
    console.log('🗺️ Initializing map with radar at:', { lat, lon });
    
    if (map) {
        map.setView([lat, lon], 10);
        if (marker) marker.setLatLng([lat, lon]);
        updateRadarLayer();
    } else {
        // Create map
        map = L.map('map', {
            center: [lat, lon],
            zoom: 10,
            zoomControl: true,
            scrollWheelZoom: true
        });
        
        // Base map - OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            maxZoom: 19
        }).addTo(map);
        
        // Add custom marker
        const markerIcon = L.divIcon({
            className: 'custom-marker',
            html: `<div style="
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 10px 16px;
                border-radius: 25px;
                font-weight: bold;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                white-space: nowrap;
                font-size: 14px;
            ">📍 ${currentCity || 'Lokasi Anda'}</div>`,
            iconSize: [150, 50],
            iconAnchor: [75, 50]
        });
        
        marker = L.marker([lat, lon], { icon: markerIcon }).addTo(map);
        
        marker.bindPopup(`
            <div style="text-align: center; padding: 10px; min-width: 200px;">
                <strong style="font-size: 18px; color: #667eea;">${currentCity || 'Lokasi Anda'}</strong><br>
                <span style="font-size: 13px; color: #666; margin-top: 8px; display: block;">
                    📍 ${lat.toFixed(4)}, ${lon.toFixed(4)}
                </span>
                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #eee;">
                    <span style="font-size: 12px; color: #999;">Klik radar untuk toggle overlay</span>
                </div>
            </div>
        `);
        
        // Add scale
        L.control.scale({
            position: 'bottomleft',
            imperial: false,
            metric: true
        }).addTo(map);
        
        // Add weather radar layers (WINDY - GRATIS & BAGUS!)
        addWindyRadarLayers();
        
        // Fix map size
        setTimeout(() => {
            map.invalidateSize();
        }, 100);
    }
}

// Add Windy Weather Radar (FREE & BEST QUALITY)
// function addWindyRadarLayers() {
//     // RADAR LAYER - Windy Precipitation (GRATIS!)
//     radarLayer = L.tileLayer('https://tiles.windy.com/tiles/v10.0/radar/{z}/{x}/{y}.png', {
//         attribution: 'Weather radar © Windy',
//         opacity: 0.7,
//         maxZoom: 19,
//         tileSize: 256
//     }).addTo(map);
    
//     // CLOUD LAYER - Satellite View (BONUS)
//     cloudLayer = L.tileLayer('https://tiles.windy.com/tiles/v10.0/satellite/{z}/{x}/{y}.jpg', {
//         attribution: 'Satellite © Windy',
//         opacity: 0.5,
//         maxZoom: 19
//     });
    
//     // Update timestamp
//     document.getElementById('radarTimestamp').textContent = `Updated: ${new Date().toLocaleTimeString('id-ID')}`;
    
//     console.log('✅ Windy radar layer added - ALWAYS ON by default');
// }

// Add Weather Radar - FINAL VERSION (GUARANTEED TO WORK!)
function addWindyRadarLayers() {
    console.log('🌧️ Loading weather radar...');
    
    // Fetch latest radar from RainViewer
    fetch('https://api.rainviewer.com/public/weather-maps.json')
        .then(response => response.json())
        .then(apiData => {
            console.log('✅ Radar API response received');
            
            if (apiData.radar && apiData.radar.past && apiData.radar.past.length > 0) {
                // Get latest radar frame
                const frames = apiData.radar.past;
                const latestFrame = frames[frames.length - 1];
                
                // Build radar URL
                // Format: /path/tile_size/z/x/y/color/smooth_snow.png
                const radarUrl = `https://tilecache.rainviewer.com${latestFrame.path}/256/{z}/{x}/{y}/6/1_1.png`;
                
                console.log('🗺️ Radar URL:', radarUrl);
                
                // Create radar layer
                radarLayer = L.tileLayer(radarUrl, {
                    attribution: 'RainViewer',
                    opacity: 0.7,
                    maxZoom: 19,
                    tileSize: 256,
                    zIndex: 1000
                }).addTo(map);
                
                // Update timestamp
                const radarTime = new Date(latestFrame.time * 1000);
                document.getElementById('radarTimestamp').textContent = 
                    `Updated: ${radarTime.toLocaleTimeString('id-ID')}`;
                
                console.log('✅ Radar loaded successfully!');
                
            } else {
                console.warn('⚠️ No radar data, using fallback');
                useFallbackRadar();
            }
        })
        .catch(error => {
            console.error('❌ Radar error:', error);
            useFallbackRadar();
        });
}

// Fallback radar
function useFallbackRadar() {
    radarLayer = L.tileLayer('https://tile.openweathermap.org/map/precipitation_new/{z}/{x}/{y}.png', {
        attribution: 'OpenWeatherMap',
        opacity: 0.6,
        maxZoom: 19,
        zIndex: 1000
    }).addTo(map);
    
    document.getElementById('radarTimestamp').textContent = 'Radar active';
    console.log('✅ Fallback radar loaded');
}


// Toggle radar layer
function toggleRadarLayer() {
    if (radarLayer) {
        if (radarEnabled) {
            map.removeLayer(radarLayer);
            document.getElementById('radarToggle').innerHTML = '🌧️ Radar: OFF';
            document.getElementById('radarToggle').classList.remove('bg-white/20');
            document.getElementById('radarToggle').classList.add('bg-white/10');
            radarEnabled = false;
        } else {
            map.addLayer(radarLayer);
            document.getElementById('radarToggle').innerHTML = '🌧️ Radar: ON';
            document.getElementById('radarToggle').classList.remove('bg-white/10');
            document.getElementById('radarToggle').classList.add('bg-white/20');
            radarEnabled = true;
        }
    }
}

// Update radar layer when coords change
function updateRadarLayer() {
    if (radarLayer && radarEnabled) {
        radarLayer.redraw();
        document.getElementById('radarTimestamp').textContent = `Updated: ${new Date().toLocaleTimeString('id-ID')}`;
    }
}

// Get city name from coordinates
function getCityNameFromCoords(lat, lon) {
    return fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`)
        .then(response => response.json())
        .then(data => {
            if (data && data.address) {
                return data.address.city || data.address.town || data.address.village || 
                       data.address.county || data.address.state || 'Lokasi Anda';
            }
            return 'Lokasi Anda';
        })
        .catch(() => 'Lokasi Anda');
}

// Load weather
function loadWeatherByCoords(lat, lon, cityName) {
    currentLat = lat;
    currentLon = lon;
    currentCity = cityName;
    
    document.getElementById('cityName').textContent = cityName;
    updateLoadingStatus('Mengambil data cuaca...');
    
    fetch(`/api/weather/current?latitude=${lat}&longitude=${lon}`)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (!data || !data.current) throw new Error('Invalid data format');
            
            displayWeather(data, cityName);
            initMap(lat, lon);
            
            @auth
            checkFavoriteStatus(lat, lon);
            @endauth
            
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('weatherContent').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Gagal Memuat Data Cuaca', error.message);
        });
}

// Display weather
function displayWeather(data, cityName) {
    const current = data.current;
    const weatherDesc = getWeatherDescription(current.weather_code);
    
    document.getElementById('weatherIcon').textContent = weatherDesc.icon;
    document.getElementById('temperature').textContent = `${Math.round(current.temperature_2m)}°`;
    document.getElementById('weatherDescription').textContent = weatherDesc.desc;
    document.getElementById('feelsLike').textContent = `Terasa seperti ${Math.round(current.apparent_temperature)}°`;
    
    if (data.daily) {
        document.getElementById('tempMax').textContent = `${Math.round(data.daily.temperature_2m_max[0])}°`;
        document.getElementById('tempMin').textContent = `${Math.round(data.daily.temperature_2m_min[0])}°`;
    }
    
    document.getElementById('humidity').textContent = `${current.relative_humidity_2m}%`;
    document.getElementById('windSpeed').textContent = `${Math.round(current.wind_speed_10m)} km/h`;
    document.getElementById('pressure').textContent = `${Math.round(current.pressure_msl)} mb`;
    document.getElementById('precipitation').textContent = `${current.precipitation || 0} mm`;
    
    if (data.daily?.sunrise && data.daily?.sunset) {
        const sunrise = new Date(data.daily.sunrise[0]);
        const sunset = new Date(data.daily.sunset[0]);
        document.getElementById('sunrise').textContent = sunrise.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
        document.getElementById('sunset').textContent = sunset.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
        updateSunProgress(data.daily.sunrise[0], data.daily.sunset[0]);
    }
    
    if (data.hourly) displayHourlyForecast(data);
    if (data.daily) displayWeeklyForecast(data);
}

// Display hourly forecast
function displayHourlyForecast(data) {
    const container = document.getElementById('hourlyForecast');
    container.innerHTML = '';
    
    for (let i = 0; i < Math.min(24, data.hourly.time.length); i++) {
        const time = new Date(data.hourly.time[i]);
        const temp = Math.round(data.hourly.temperature_2m[i]);
        const weather = getWeatherDescription(data.hourly.weather_code[i]);
        
        container.innerHTML += `
            <div class="flex-shrink-0 bg-white/20 backdrop-blur-lg rounded-2xl p-4 text-center text-white min-w-[80px]">
                <div class="text-xs font-semibold mb-2">${time.getHours()}:00</div>
                <div class="text-3xl mb-2">${weather.icon}</div>
                <div class="text-lg font-bold">${temp}°</div>
            </div>
        `;
    }
}

// Display weekly forecast
function displayWeeklyForecast(data) {
    const container = document.getElementById('weeklyForecast');
    container.innerHTML = '';
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    
    for (let i = 0; i < Math.min(7, data.daily.time.length); i++) {
        const date = new Date(data.daily.time[i]);
        const dayName = i === 0 ? 'Hari ini' : days[date.getDay()];
        const weather = getWeatherDescription(data.daily.weather_code[i]);
        const maxTemp = Math.round(data.daily.temperature_2m_max[i]);
        const minTemp = Math.round(data.daily.temperature_2m_min[i]);
        
        container.innerHTML += `
            <div class="flex items-center justify-between bg-white/10 backdrop-blur-lg rounded-xl p-4 text-white">
                <div class="flex-1 font-semibold">${dayName}</div>
                <div class="flex items-center gap-4 flex-1 justify-center">
                    <span class="text-3xl">${weather.icon}</span>
                </div>
                <div class="flex gap-3 flex-1 justify-end">
                    <span class="font-bold">${maxTemp}°</span>
                    <span class="opacity-60">${minTemp}°</span>
                </div>
            </div>
        `;
    }
}

// Update sun progress
function updateSunProgress(sunrise, sunset) {
    const now = new Date();
    const sunriseTime = new Date(sunrise);
    const sunsetTime = new Date(sunset);
    
    if (now < sunriseTime) {
        document.getElementById('sunProgress').style.width = '0%';
    } else if (now > sunsetTime) {
        document.getElementById('sunProgress').style.width = '100%';
    } else {
        const total = sunsetTime - sunriseTime;
        const current = now - sunriseTime;
        const percentage = (current / total) * 100;
        document.getElementById('sunProgress').style.width = percentage + '%';
    }
}

@auth
function checkFavoriteStatus(lat, lon) {
    fetch('/api/favorites/check', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ latitude: lat, longitude: lon })
    })
    .then(response => response.json())
    .then(data => {
        isFavorite = data.is_favorite;
        updateFavoriteButton();
    });
}

function toggleFavorite() {
    if (!currentLat || !currentLon) return;
    
    const url = isFavorite ? '/api/favorites/remove' : '/api/favorites/add';
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            city_name: currentCity,
            latitude: currentLat,
            longitude: currentLon,
            country: 'Indonesia'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            isFavorite = !isFavorite;
            updateFavoriteButton();
            alert(data.message);
        }
    });
}

function updateFavoriteButton() {
    const btn = document.getElementById('favoriteBtn');
    if (btn) {
        btn.textContent = isFavorite ? '⭐' : '☆';
        btn.className = `favorite-btn text-white hover:bg-white/20 p-3 rounded-lg transition text-2xl ${isFavorite ? 'active' : ''}`;
    }
}
@endauth

function getWeatherDescription(code) {
    const descriptions = {
        0: { desc: 'Cerah', icon: '☀️' },
        1: { desc: 'Cerah Sebagian', icon: '🌤️' },
        2: { desc: 'Berawan Sebagian', icon: '⛅' },
        3: { desc: 'Berawan', icon: '☁️' },
        45: { desc: 'Berkabut', icon: '🌫️' },
        48: { desc: 'Kabut Beku', icon: '🌫️' },
        51: { desc: 'Gerimis Ringan', icon: '🌦️' },
        53: { desc: 'Gerimis Sedang', icon: '🌦️' },
        55: { desc: 'Gerimis Lebat', icon: '🌧️' },
        61: { desc: 'Hujan Ringan', icon: '🌧️' },
        63: { desc: 'Hujan Sedang', icon: '🌧️' },
        65: { desc: 'Hujan Lebat', icon: '⛈️' },
        80: { desc: 'Hujan Rintik', icon: '🌦️' },
        81: { desc: 'Hujan Deras', icon: '⛈️' },
        95: { desc: 'Petir', icon: '⚡' }
    };
    return descriptions[code] || { desc: 'Tidak Diketahui', icon: '❓' };
}

// Auto-detect location
document.addEventListener('DOMContentLoaded', function() {
    updateTime();
    updateTheme();
    setInterval(updateTime, 60000);
    setInterval(updateTheme, 60000);
    
    const urlParams = new URLSearchParams(window.location.search);
    const lat = urlParams.get('lat');
    const lon = urlParams.get('lon');
    const city = urlParams.get('city');
    
    if (lat && lon) {
        loadWeatherByCoords(parseFloat(lat), parseFloat(lon), decodeURIComponent(city || 'Lokasi'));
    } else if (navigator.geolocation) {
        updateLoadingStatus('🌍 Mendeteksi lokasi Anda...');
        
        navigator.geolocation.getCurrentPosition(
            async position => {
                currentLat = position.coords.latitude;
                currentLon = position.coords.longitude;
                const cityName = await getCityNameFromCoords(currentLat, currentLon);
                loadWeatherByCoords(currentLat, currentLon, cityName);
            },
            error => {
                console.error('Geolocation error:', error);
                updateLoadingStatus('Menggunakan lokasi default Jakarta...');
                setTimeout(() => loadWeatherByCoords(-6.2088, 106.8456, 'Jakarta'), 2000);
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    } else {
        loadWeatherByCoords(-6.2088, 106.8456, 'Jakarta');
    }
});
</script>
@endpush
@endsection
