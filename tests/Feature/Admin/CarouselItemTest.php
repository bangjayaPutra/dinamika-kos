<?php

use App\Models\CarouselItem;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('guests are redirected to login', function () {
    $this->get(route('admin.carousel-items.index'))->assertRedirect(route('login'));
});

test('non-admin users are forbidden', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.carousel-items.index'))->assertForbidden();

    $this->actingAs($user)->post(route('admin.carousel-items.store'), [])->assertForbidden();
});

test('admins can view the carousel list', function () {
    CarouselItem::factory()->count(2)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.carousel-items.index'))
        ->assertOk();
});

test('admins can create a carousel item with an image', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.carousel-items.store'), [
            'title' => 'Promo Baru',
            'subtitle' => 'Keterangan promo',
            'image' => UploadedFile::fake()->image('banner.jpg'),
            'sort_order' => 1,
            'is_active' => true,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.carousel-items.index'));

    $item = CarouselItem::where('title', 'Promo Baru')->firstOrFail();

    expect($item->is_active)->toBeTrue();

    Storage::disk('public')->assertExists($item->image_path);
});

test('image is required when creating a carousel item', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.carousel-items.store'), [
            'title' => 'Tanpa Gambar',
        ])
        ->assertSessionHasErrors('image');

    expect(CarouselItem::count())->toBe(0);
});

test('admins can update a carousel item without replacing the image', function () {
    $item = CarouselItem::factory()->create(['title' => 'Lama']);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.carousel-items.update', $item), [
            'title' => 'Baru',
            'sort_order' => 3,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.carousel-items.index'));

    expect($item->refresh()->title)->toBe('Baru')
        ->and($item->refresh()->sort_order)->toBe(3);
});

test('admins can delete a carousel item along with its image', function () {
    Storage::disk('public')->put('carousel/banner.jpg', 'dummy');

    $item = CarouselItem::factory()->create(['image_path' => 'carousel/banner.jpg']);

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('admin.carousel-items.destroy', $item))
        ->assertRedirect(route('admin.carousel-items.index'));

    expect($item->fresh())->toBeNull();

    Storage::disk('public')->assertMissing('carousel/banner.jpg');
});

test('homepage shows only active carousel items in order', function () {
    $second = CarouselItem::factory()->create(['title' => 'Kedua', 'sort_order' => 2]);
    $first = CarouselItem::factory()->create(['title' => 'Pertama', 'sort_order' => 1]);
    $hidden = CarouselItem::factory()->inactive()->create(['title' => 'Sembunyi']);

    $response = $this->get(route('home'));

    $response->assertOk();

    $slides = collect($response->viewData('page')['props']['carousel']);

    expect($slides->pluck('title')->all())->toBe(['Pertama', 'Kedua']);
    expect($slides->pluck('id')->all())->not->toContain($hidden->id);
});
