@extends('layouts.app')

@section('title', 'Riwayat Pencarian')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
    
    .history-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(20px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s;
    }
    
    .history-card:hover {
        transform: translateX(4px);
        background: rgba(255, 255, 255, 0.25);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">🕐 Riwayat Pencarian</h1>
                <p class="text-white opacity-80">Kota-kota yang pernah Anda cari</p>
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

        @if($histories->isNotEmpty())
            <div class="mb-6 flex justify-end">
                <form method="POST" action="{{ route('history.destroyAll') }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Hapus semua riwayat?')" 
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-xl font-semibold transition">
                        🗑️ Hapus Semua
                    </button>
                </form>
            </div>
        @endif

        @if($histories->isEmpty())
            <div class="text-center py-20">
                <div class="text-8xl mb-4">📭</div>
                <h2 class="text-2xl font-bold text-white mb-2">Belum Ada Riwayat</h2>
                <p class="text-white opacity-80 mb-6">Mulai cari kota untuk melihat cuaca</p>
                <a href="{{ route('home') }}" class="inline-block bg-white text-purple-600 px-8 py-3 rounded-xl font-bold hover:bg-gray-100 transition">
                    🔍 Cari Kota
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach($histories as $history)
                    <div class="history-card p-4 text-white flex items-center justify-between">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="text-3xl">🌍</div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold">{{ $history->city_name }}</h3>
                                <div class="flex items-center gap-4 text-sm opacity-80 mt-1">
                                    <span>{{ $history->country ?? 'Unknown' }}</span>
                                    <span>•</span>
                                    <span>{{ $history->created_at->diffForHumans() }}</span>
                                    <span>•</span>
                                    <span>{{ $history->created_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="/?lat={{ $history->latitude }}&lon={{ $history->longitude }}&city={{ urlencode($history->city_name) }}" 
                                class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg font-semibold transition">
                                👁️ Lihat
                            </a>
                            <form method="POST" action="{{ route('history.destroy', $history) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus riwayat ini?')" 
                                    class="bg-red-500/20 hover:bg-red-500/30 text-white px-4 py-2 rounded-lg transition">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $histories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
