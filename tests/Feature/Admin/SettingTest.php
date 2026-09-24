<?php

use App\Models\Setting;
use App\Models\User;

test('guests are redirected to login', function () {
    $this->get(route('admin.settings.edit'))->assertRedirect(route('login'));
});

test('non-admin users are forbidden', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.settings.edit'))->assertForbidden();

    $this->actingAs($user)->put(route('admin.settings.update'), [])->assertForbidden();
});

test('admins can view the settings form', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.settings.edit'))
        ->assertOk();
});

test('admins can update settings', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.settings.update'), [
            'site_name' => 'Kos Baru',
            'tagline' => 'Slogan baru',
            'address' => 'Jl. Contoh No. 1',
            'wa_number' => '6281234567890',
            'maps_url' => 'https://maps.google.com/?q=kos',
            'welcome_text' => 'Selamat datang.',
            'operating_hours' => 'Setiap hari',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.settings.edit'));

    expect(Setting::get('site_name'))->toBe('Kos Baru')
        ->and(Setting::get('wa_number'))->toBe('6281234567890');
});

test('site name is required', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.settings.update'), ['site_name' => ''])
        ->assertSessionHasErrors('site_name');
});
