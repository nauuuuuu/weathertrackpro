<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Auto-create preference if not exists
        if (!$user->preference) {
            $user->preference()->create([
                'temperature_unit' => 'celsius',
                'wind_speed_unit' => 'kmh',
                'auto_location' => true,
            ]);
            $user->load('preference'); // Reload relationship
        }
        
        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return redirect()->route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update user preferences
     */
    public function updatePreferences(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'temperature_unit' => 'required|in:celsius,fahrenheit',
            'wind_speed_unit' => 'required|in:kmh,ms,mph',
            'auto_location' => 'nullable|boolean',
        ]);

        $user = $request->user();
        
        // Auto-create if not exists
        if (!$user->preference) {
            $user->preference()->create($validated);
        } else {
            $user->preference->update($validated);
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Preferensi berhasil diperbarui!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Password berhasil diubah!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
