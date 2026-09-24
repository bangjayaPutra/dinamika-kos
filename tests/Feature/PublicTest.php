<?php

use App\Models\Post;
use App\Models\Review;
use App\Models\RoomType;
use App\Models\WaClick;

test('guests can view the homepage', function () {
    $this->get(route('home'))->assertOk();
});

test('guests can view the room type list and detail', function () {
    $roomType = RoomType::factory()->create();

    $this->get(route('public.room-types.index'))->assertOk();

    $this->get(route('public.room-types.show', $roomType->slug))->assertOk();
});

test('unknown room type slugs return 404', function () {
    $this->get(route('public.room-types.show', 'tidak-ada'))->assertNotFound();
});

test('guests can view the facilities page', function () {
    $this->get(route('public.facilities.index'))->assertOk();
});

test('only published posts are visible to guests', function () {
    $published = Post::factory()->published()->create(['is_published' => true, 'published_at' => now()]);
    $draft = Post::factory()->create(['is_published' => false, 'published_at' => null]);

    $this->get(route('public.posts.index'))->assertOk();

    $this->get(route('public.posts.show', $published->slug))->assertOk();

    $this->get(route('public.posts.show', $draft->slug))->assertNotFound();
});

test('guests can view the contact page', function () {
    $this->get(route('public.contact'))->assertOk();
});

test('guests can submit a review shown immediately', function () {
    $roomType = RoomType::factory()->create();

    $response = $this->post(route('reviews.store'), [
        'nama' => 'Budi',
        'rating' => 5,
        'room_type_id' => $roomType->id,
        'body' => 'Kosnya nyaman dan bersih.',
    ]);

    $response->assertSessionHasNoErrors()->assertRedirect();

    $review = Review::where('nama', 'Budi')->firstOrFail();

    expect($review->room_type_id)->toBe($roomType->id);

    $home = $this->get(route('home'))->assertOk();

    $names = collect($home->viewData('page')['props']['reviews'])->pluck('nama');

    expect($names)->toContain('Budi');
});

test('review ratings must be between 1 and 5', function () {
    $this->post(route('reviews.store'), [
        'nama' => 'Budi',
        'rating' => 9,
        'body' => 'Isi ulasan.',
    ])->assertSessionHasErrors('rating');

    expect(Review::count())->toBe(0);
});

test('whatsapp clicks are recorded', function () {
    $roomType = RoomType::factory()->create();

    $this->post(route('wa-clicks.store'), [
        'room_type_id' => $roomType->id,
    ])->assertRedirect();

    $click = WaClick::firstOrFail();

    expect($click->room_type_id)->toBe($roomType->id)
        ->and($click->ip_address)->not->toBeNull();
});
