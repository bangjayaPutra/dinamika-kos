<?php

use App\Models\RoomType;
use App\Models\User;
use App\Models\WaClick;
use Carbon\Carbon;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('dashboard shows daily whatsapp clicks for the selected month', function () {
    WaClick::factory()->count(2)->create(['created_at' => Carbon::create(2025, 3, 5, 10)]);
    WaClick::factory()->create(['created_at' => Carbon::create(2025, 3, 20, 10)]);
    WaClick::factory()->create(['created_at' => Carbon::create(2025, 4, 5, 10)]);

    $response = $this->actingAs(User::factory()->create())
        ->get(route('dashboard', ['month' => 3, 'year' => 2025]));

    $response->assertOk();

    $props = $response->viewData('page')['props'];

    expect($props['filter'])->toBe(['month' => 3, 'year' => 2025])
        ->and($props['stats']['wa_clicks'])->toBe(3)
        ->and(collect($props['daily'])->firstWhere('day', 5)['total'])->toBe(2)
        ->and(collect($props['daily'])->firstWhere('day', 20)['total'])->toBe(1)
        ->and(collect($props['daily'])->firstWhere('day', 6)['total'])->toBe(0)
        ->and($props['daily'])->toHaveCount(31);
});

test('dashboard groups clicks by room type', function () {
    $roomType = RoomType::factory()->create(['name' => 'Standar']);
    WaClick::factory()->forRoomType($roomType)->create(['created_at' => Carbon::create(2025, 3, 5, 10)]);
    WaClick::factory()->create(['created_at' => Carbon::create(2025, 3, 6, 10)]);

    $response = $this->actingAs(User::factory()->create())
        ->get(route('dashboard', ['month' => 3, 'year' => 2025]));

    $byRoomType = collect($response->viewData('page')['props']['byRoomType']);

    expect($byRoomType->firstWhere('name', 'Standar')['total'])->toBe(1)
        ->and($byRoomType->firstWhere('name', 'Umum')['total'])->toBe(1);
});

test('dashboard clamps invalid month and year filters', function () {
    $response = $this->actingAs(User::factory()->create())
        ->get(route('dashboard', ['month' => 99, 'year' => 1999]));

    $response->assertOk();

    expect($response->viewData('page')['props']['filter'])->toBe([
        'month' => 12,
        'year' => 2020,
    ]);
});
