<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // HAPUS __construct() yang lama
    
    public function index()
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $users = User::where('role', 'user')
            ->withCount(['favoriteCities', 'searchHistories'])
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $user->load(['favoriteCities', 'searchHistories' => function ($query) {
            $query->latest()->take(20);
        }]);

        return view('admin.users.show', compact('user'));
    }

    public function toggleActive(User $user)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menonaktifkan akun admin'
            ], 403);
        }

        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success' => true,
            'message' => $user->is_active ? 'User berhasil diaktifkan' : 'User berhasil dinonaktifkan',
            'is_active' => $user->is_active
        ]);
    }
}
