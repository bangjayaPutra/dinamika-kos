<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    /**
     * Show the form for editing site settings.
     */
    public function edit(): Response
    {
        return Inertia::render('admin/Settings/Edit', [
            'settings' => Setting::allAsMap(),
        ]);
    }

    /**
     * Update the site settings in storage.
     */
    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        foreach ($request->validated() as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Pengaturan diperbarui.']);

        return to_route('admin.settings.edit');
    }
}
