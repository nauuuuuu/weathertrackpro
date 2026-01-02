@extends('layouts.app')

@section('title', 'Manajemen Users')

@section('content')
<div class="py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="weather-card p-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">👥 Manajemen Users</h1>
                    <p class="text-gray-600 mt-1">Kelola semua pengguna WeatherTrackPro</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                    ← Kembali
                </a>
            </div>
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg">
                    <p class="font-bold">Berhasil!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-blue-500 to-purple-600 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold">Nama</th>
                            <th class="px-6 py-4 text-left font-semibold">Email</th>
                            <th class="px-6 py-4 text-left font-semibold">Terdaftar</th>
                            <th class="px-6 py-4 text-center font-semibold">Favorit</th>
                            <th class="px-6 py-4 text-center font-semibold">Pencarian</th>
                            <th class="px-6 py-4 text-center font-semibold">Status</th>
                            <th class="px-6 py-4 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-gray-800">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">{{ $user->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                        ⭐ {{ $user->favorite_cities_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                                        🔍 {{ $user->search_histories_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-4 py-2 rounded-full text-sm font-bold {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}" id="status-{{ $user->id }}">
                                        {{ $user->is_active ? '✅ Aktif' : '❌ Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('admin.users.show', $user) }}" 
                                            class="bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600 transition" 
                                            title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <button onclick="toggleUserStatus({{ $user->id }}, {{ $user->is_active ? 'true' : 'false' }})" 
                                            class="bg-{{ $user->is_active ? 'red' : 'green' }}-500 text-white p-2 rounded-lg hover:bg-{{ $user->is_active ? 'red' : 'green' }}-600 transition"
                                            title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($user->is_active)
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                @endif
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="text-6xl mb-4">👤</div>
                                    <p class="text-lg font-semibold">Belum ada user terdaftar</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleUserStatus(userId, currentStatus) {
    const action = currentStatus ? 'menonaktifkan' : 'mengaktifkan';
    
    if (!confirm(`Apakah Anda yakin ingin ${action} user ini?`)) {
        return;
    }
    
    fetch(`/admin/users/${userId}/toggle-active`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}
</script>
@endpush
@endsection
