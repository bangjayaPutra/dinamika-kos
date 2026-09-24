<?php

use App\Models\Facility;
use App\Models\User;

test('guests are redirected to login', function () {
    $this->get(route('admin.facilities.index'))->assertRedirect(route('login'));
});

test('non-admin users are forbidden', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.facilities.index'))->assertForbidden();

    $this->actingAs($user)->post(route('admin.facilities.store'), [])->assertForbidden();
});

test('admins can view the facility list', function () {
    Facility::factory()->count(2)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.facilities.index'))
        ->assertOk();
});

test('admins can create a facility', function () {
    $response = $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.facilities.store'), [
            'name' => 'WiFi',
            'icon' => 'wifi',
            'description' => 'Internet cepat di semua kamar.',
            'scope' => 'kamar',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.facilities.index'));

    $facility = Facility::where('name', 'WiFi')->first();

    expect($facility)->not->toBeNull()
        ->and($facility->scope)->toBe('kamar');
});

test('facility creation requires valid data', function () {
    $response = $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.facilities.store'), []);

    $response->assertSessionHasErrors(['name', 'scope']);
});

test('facility scope must be kamar or umum', function () {
    $response = $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.facilities.store'), [
            'name' => 'Parkir',
            'scope' => 'invalid',
        ]);

    $response->assertSessionHasErrors(['scope']);
});

test('admins can update a facility', function () {
    $facility = Facility::factory()->kamar()->create(['name' => 'Fasilitas Lama']);

    $response = $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.facilities.update', $facility), [
            'name' => 'Fasilitas Baru',
            'icon' => null,
            'description' => $facility->description,
            'scope' => 'umum',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.facilities.index'));

    expect($facility->refresh())
        ->name->toBe('Fasilitas Baru')
        ->scope->toBe('umum');
});

test('admins can delete a facility', function () {
    $facility = Facility::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('admin.facilities.destroy', $facility))
        ->assertRedirect(route('admin.facilities.index'));

    expect($facility->fresh())->toBeNull();
});
