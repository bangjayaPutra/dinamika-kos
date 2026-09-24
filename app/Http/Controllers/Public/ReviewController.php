<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewController extends Controller
{
    /**
     * Store a visitor review shown immediately without moderation.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'room_type_id' => ['nullable', 'integer', 'exists:room_types,id'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        Review::create([
            ...$validated,
            'ip_address' => $request->ip(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Terima kasih! Ulasanmu sudah tampil.']);

        return back();
    }
}
