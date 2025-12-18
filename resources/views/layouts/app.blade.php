<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WeatherTrackPro')</title>
    
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Figtree', sans-serif; overflow-x: hidden; }
        
        .sidebar {
            position: fixed;
            left: -320px;
            top: 0;
            width: 320px;
            height: 100vh;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            backdrop-filter: blur(20px);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.2);
            transition: left 0.3s ease;
            z-index: 9999;
            overflow-y: auto;
        }
        
        .sidebar.active { left: 0; }
        
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 9998;
        }
        
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .search-input {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 12px 16px;
            border-radius: 12px;
            width: 100%;
            backdrop-filter: blur(10px);
        }
        
        .search-input::placeholder { color: rgba(255, 255, 255, 0.7); }
        .search-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.2s;
            border-radius: 12px;
            margin: 4px 0;
        }
        
        .menu-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(4px);
        }
        
        .menu-item.active {
            background: rgba(255, 255, 255, 0.25);
            font-weight: 600;
        }
        
        .city-result {
            padding: 12px;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.2s;
            color: white;
            background: rgba(255, 255, 255, 0.1);
            margin-bottom: 8px;
        }
        
        .city-result:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateX(4px);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="p-6">
            <!-- Header Sidebar -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="text-4xl">🌤️</div>
                    <div>
                        <h2 class="text-white text-xl font-bold">WeatherTrackPro</h2>
                        <p class="text-white text-xs opacity-80">Real-time Weather</p>
                    </div>
                </div>
                <button onclick="toggleSidebar()" class="text-white hover:bg-white/20 p-2 rounded-lg transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Search Box -->
            <div class="mb-6">
                <div class="relative">
                    <input type="text" 
                        id="citySearchInput" 
                        class="search-input" 
                        placeholder="🔍 Cari kota..."
                        autocomplete="off">
                    <div id="searchResults" class="mt-2 max-h-64 overflow-y-auto"></div>
                </div>
            </div>
            
            <!-- Menu Navigation -->
            <div class="mb-6">
                <p class="text-white text-xs uppercase opacity-60 mb-2 px-2">Menu Utama</p>
                <a href="{{ route('home') }}" class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard Cuaca</span>
                </a>
                
                @auth
                <a href="{{ route('favorites.index') }}" class="menu-item {{ request()->routeIs('favorites.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    <span>Kota Favorit</span>
                </a>
                
                <a href="{{ route('history.index') }}" class="menu-item {{ request()->routeIs('history.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Riwayat Pencarian</span>
                </a>
                
                <a href="{{ route('profile.edit') }}" class="menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profil Saya</span>
                </a>
                
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span>Admin Panel</span>
                </a>
                @endif
                @endauth
            </div>
            
            <!-- User Section -->
            <div class="border-t border-white/20 pt-4">
                @auth
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-4 mb-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-white font-semibold">{{ auth()->user()->name }}</p>
                            <p class="text-white text-xs opacity-70">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg transition font-semibold">
                            🚪 Logout
                        </button>
                    </form>
                </div>
                @else
                <div class="space-y-2">
                    <a href="{{ route('login') }}" class="block w-full bg-white text-purple-600 py-3 px-4 rounded-lg text-center font-bold hover:bg-gray-100 transition">
                        🔐 Login
                    </a>
                    <a href="{{ route('register') }}" class="block w-full bg-purple-500 hover:bg-purple-600 text-white py-3 px-4 rounded-lg text-center font-bold transition">
                        ✨ Daftar Gratis
                    </a>
                </div>
                @endauth
            </div>
            
            <!-- Footer Info -->
            <div class="mt-6 text-center text-white text-xs opacity-60">
                <p>WeatherTrackPro v1.0</p>
                <p>Powered by Open-Meteo API</p>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div id="mainContent">
        @yield('content')
    </div>
    
    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }
        
        // City Search
        let searchTimeout;
        document.getElementById('citySearchInput')?.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            if (query.length < 2) {
                document.getElementById('searchResults').innerHTML = '';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(query)}&count=10&language=id&format=json`)
                    .then(response => response.json())
                    .then(data => {
                        const results = document.getElementById('searchResults');
                        results.innerHTML = '';
                        
                        if (data.results && data.results.length > 0) {
                            data.results.forEach(city => {
                                const div = document.createElement('div');
                                div.className = 'city-result';
                                div.innerHTML = `
                                    <div class="font-semibold">${city.name}</div>
                                    <div class="text-sm opacity-80">${city.admin1 ? city.admin1 + ', ' : ''}${city.country}</div>
                                `;
                                div.onclick = () => {
                                    window.location.href = `/?lat=${city.latitude}&lon=${city.longitude}&city=${encodeURIComponent(city.name)}`;
                                };
                                results.appendChild(div);
                            });
                        } else {
                            results.innerHTML = '<p class="text-white text-sm opacity-70 p-2">Kota tidak ditemukan</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                    });
            }, 300);
        });
    </script>
    @stack('scripts')
</body>
</html>
