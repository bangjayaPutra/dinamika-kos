<?php

namespace App\Http\Controllers;

use App\Models\WaClick;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WaClickController extends Controller
{
    /**
     * Record a WhatsApp button click.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_type_id' => ['nullable', 'integer', 'exists:room_types,id'],
        ]);

        WaClick::create([
            'room_type_id' => $validated['room_type_id'] ?? null,
            'ip_address' => $request->ip(),
        ]);

        return back();
    }
}
