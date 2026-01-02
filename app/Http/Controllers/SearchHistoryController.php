<?php

namespace App\Http\Controllers;

use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;

class SearchHistoryController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, string>
     */
    public static function middleware(): array
    {
        return ['auth'];
    }

    /**
     * Display search history
     *
     * @return View
     */
    public function index(): View
    {
        $histories = auth()->user()->searchHistories()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('history.index', compact('histories'));
    }

    /**
     * Delete specific history
     *
     * @param SearchHistory $history
     * @return RedirectResponse
     */
    public function destroy(SearchHistory $history): RedirectResponse
    {
        // Make sure the history belongs to the authenticated user
        if ($history->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $history->delete();

        return redirect()->route('history.index')
            ->with('success', 'Riwayat berhasil dihapus');
    }

    /**
     * Delete all history
     *
     * @return RedirectResponse
     */
    public function destroyAll(): RedirectResponse
    {
        auth()->user()->searchHistories()->delete();

        return redirect()->route('history.index')
            ->with('success', 'Semua riwayat berhasil dihapus');
    }
}
