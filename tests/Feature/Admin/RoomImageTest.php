<?php

use App\Models\RoomImage;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('guests are redirected to login', function () {
    $roomType = RoomType::factory()->create();

    $this->post(route('admin.room-images.store', $roomType), [])->assertRedirect(route('login'));
});

test('non-admin users are forbidden', function () {
    $roomType = RoomType::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('admin.room-images.store', $roomType), [])
        ->assertForbidden();
});

test('admins can upload multiple images', function () {
    $roomType = RoomType::factory()->create();

    $response = $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.room-images.store', $roomType), [
            'images' => [
                UploadedFile::fake()->image('kamar-1.jpg'),
                UploadedFile::fake()->image('kamar-2.png'),
            ],
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.room-types.edit', $roomType));

    expect($roomType->images()->count())->toBe(2);

    $roomType->images()->ordered()->each(function (RoomImage $image): void {
        Storage::disk('public')->assertExists($image->path);
    });
});

test('the first uploaded image becomes the cover', function () {
    $roomType = RoomType::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.room-images.store', $roomType), [
            'images' => [UploadedFile::fake()->image('kamar-1.jpg')],
        ])
        ->assertSessionHasNoErrors();

    expect($roomType->images()->where('is_cover', true)->count())->toBe(1);
});

test('image uploads reject invalid files', function () {
    $roomType = RoomType::factory()->create();

    $response = $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.room-images.store', $roomType), [
            'images' => [UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf')],
        ]);

    $response->assertSessionHasErrors('images.0');

    expect($roomType->images()->count())->toBe(0);
});

test('image uploads respect the six image limit', function () {
    $roomType = RoomType::factory()->create();
    RoomImage::factory()->count(5)->create(['room_type_id' => $roomType->id]);

    $response = $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.room-images.store', $roomType), [
            'images' => [
                UploadedFile::fake()->image('kamar-6.jpg'),
                UploadedFile::fake()->image('kamar-7.jpg'),
            ],
        ]);

    $response->assertSessionHasErrors('images');

    expect($roomType->images()->count())->toBe(5);
});

test('admins can change the cover image', function () {
    $roomType = RoomType::factory()->create();
    $oldCover = RoomImage::factory()->cover()->create(['room_type_id' => $roomType->id]);
    $newCover = RoomImage::factory()->create(['room_type_id' => $roomType->id]);

    $this->actingAs(User::factory()->admin()->create())
        ->patch(route('admin.room-images.cover', $newCover))
        ->assertRedirect(route('admin.room-types.edit', $roomType));

    expect($newCover->refresh()->is_cover)->toBeTrue()
        ->and($oldCover->refresh()->is_cover)->toBeFalse()
        ->and($roomType->images()->where('is_cover', true)->count())->toBe(1);
});

test('admins can delete an image including its file', function () {
    $roomType = RoomType::factory()->create();
    $image = RoomImage::factory()->create([
        'room_type_id' => $roomType->id,
        'path' => UploadedFile::fake()->image('kamar-1.jpg')->store('room-images', 'public'),
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('admin.room-images.destroy', $image))
        ->assertRedirect(route('admin.room-types.edit', $roomType));

    Storage::disk('public')->assertMissing($image->path);

    expect(RoomImage::find($image->id))->toBeNull();
});

test('deleting the cover promotes the next image', function () {
    $roomType = RoomType::factory()->create();
    $cover = RoomImage::factory()->cover()->create(['room_type_id' => $roomType->id, 'sort_order' => 0]);
    $next = RoomImage::factory()->create(['room_type_id' => $roomType->id, 'sort_order' => 1]);

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('admin.room-images.destroy', $cover))
        ->assertSessionHasNoErrors();

    expect($next->refresh()->is_cover)->toBeTrue();
});
