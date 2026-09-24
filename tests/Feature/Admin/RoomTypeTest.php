<?php

use App\Models\Facility;
use App\Models\RoomType;
use App\Models\User;

test('guests are redirected to login', function () {
    $this->get(route('admin.room-types.index'))->assertRedirect(route('login'));
});

test('non-admin users are forbidden', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.room-types.index'))->assertForbidden();

    $this->actingAs($user)->post(route('admin.room-types.store'), [])->assertForbidden();
});

test('admins can view the room type list', function () {
    RoomType::factory()->count(2)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.room-types.index'))
        ->assertOk();
});

test('admins can create a room type', function () {
    $response = $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.room-types.store'), [
            'name' => 'Kamar Standar',
            'description' => 'Kamar nyaman untuk 1 orang.',
            'price_monthly' => 1500000,
            'size_label' => '3x4 m',
            'capacity' => 1,
            'stock_total' => 10,
            'is_available' => true,
            'sort_order' => 1,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.room-types.index'));

    $roomType = RoomType::where('name', 'Kamar Standar')->first();

    expect($roomType)->not->toBeNull()
        ->and($roomType->slug)->toBe('kamar-standar')
        ->and($roomType->price_monthly)->toBe(1500000);
});

test('room type slugs stay unique', function () {
    RoomType::factory()->create(['name' => 'Kamar Standar', 'slug' => 'kamar-standar']);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.room-types.store'), [
            'name' => 'Kamar Standar',
            'price_monthly' => 1500000,
            'capacity' => 1,
            'stock_total' => 5,
        ])
        ->assertSessionHasNoErrors();

    expect(RoomType::where('slug', 'kamar-standar-1')->exists())->toBeTrue();
});

test('room type creation requires valid data', function () {
    $response = $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.room-types.store'), []);

    $response->assertSessionHasErrors(['name', 'price_monthly', 'capacity', 'stock_total']);
});

test('admins can update a room type', function () {
    $roomType = RoomType::factory()->create(['name' => 'Kamar Lama', 'slug' => 'kamar-lama']);

    $response = $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.room-types.update', $roomType), [
            'name' => 'Kamar Baru',
            'description' => $roomType->description,
            'price_monthly' => 2000000,
            'size_label' => $roomType->size_label,
            'capacity' => 2,
            'stock_total' => 8,
            'is_available' => false,
            'sort_order' => 2,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.room-types.index'));

    expect($roomType->refresh())
        ->name->toBe('Kamar Baru')
        ->slug->toBe('kamar-baru')
        ->price_monthly->toBe(2000000)
        ->is_available->toBeFalse();
});

test('admins can delete a room type', function () {
    $roomType = RoomType::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('admin.room-types.destroy', $roomType))
        ->assertRedirect(route('admin.room-types.index'));

    expect($roomType->fresh())->toBeNull();
});

test('admins can attach facilities when creating a room type', function () {
    $facilities = Facility::factory()->kamar()->count(2)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.room-types.store'), [
            'name' => 'Kamar Deluxe',
            'price_monthly' => 2000000,
            'capacity' => 2,
            'stock_total' => 5,
            'facilities' => $facilities->pluck('id')->all(),
        ])
        ->assertSessionHasNoErrors();

    $roomType = RoomType::where('name', 'Kamar Deluxe')->first();

    expect($roomType->facilities()->pluck('facilities.id')->sort()->values()->all())
        ->toBe($facilities->pluck('id')->sort()->values()->all());
});

test('admins can sync facilities when updating a room type', function () {
    $roomType = RoomType::factory()->create();
    $oldFacility = Facility::factory()->kamar()->create();
    $roomType->facilities()->attach($oldFacility);
    $newFacilities = Facility::factory()->kamar()->count(2)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.room-types.update', $roomType), [
            'name' => $roomType->name,
            'price_monthly' => $roomType->price_monthly,
            'capacity' => $roomType->capacity,
            'stock_total' => $roomType->stock_total,
            'facilities' => $newFacilities->pluck('id')->all(),
        ])
        ->assertSessionHasNoErrors();

    expect($roomType->refresh()->facilities()->pluck('facilities.id')->sort()->values()->all())
        ->toBe($newFacilities->pluck('id')->sort()->values()->all());
});

test('room type creation rejects unknown facilities', function () {
    $response = $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.room-types.store'), [
            'name' => 'Kamar Deluxe',
            'price_monthly' => 2000000,
            'capacity' => 2,
            'stock_total' => 5,
            'facilities' => [999999],
        ]);

    $response->assertSessionHasErrors('facilities.0');
});
