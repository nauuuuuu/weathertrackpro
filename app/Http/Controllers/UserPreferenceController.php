<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    // HAPUS __construct() yang lama
    
    public function edit()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $preference = auth()->user()->preference ?? auth()->user()->preference()->create([]);
        return view('preferences.edit', compact('preference'));
    }

    public function update(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'temperature_unit' => 'required|in:celsius,fahrenheit',
            'wind_speed_unit' => 'required|in:kmh,ms,mph',
            'auto_location' => 'boolean',
        ]);

        $preference = auth()->user()->preference ?? auth()->user()->preference()->create([]);
        
        $preference->update([
            'temperature_unit' => $request->temperature_unit,
            'wind_speed_unit' => $request->wind_speed_unit,
            'auto_location' => $request->has('auto_location'),
        ]);

        return redirect()->back()->with('success', 'Preferensi berhasil diperbarui');
    }
}
