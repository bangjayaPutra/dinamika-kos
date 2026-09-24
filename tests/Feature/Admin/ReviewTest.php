<?php

use App\Models\Review;
use App\Models\User;

test('guests are redirected to login', function () {
    $this->get(route('admin.reviews.index'))->assertRedirect(route('login'));
});

test('non-admin users are forbidden', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.reviews.index'))->assertForbidden();

    $review = Review::factory()->create();

    $this->actingAs($user)->delete(route('admin.reviews.destroy', $review))->assertForbidden();
});

test('admins can view the review list', function () {
    Review::factory()->count(2)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.reviews.index'))
        ->assertOk();
});

test('admins can delete a review', function () {
    $review = Review::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('admin.reviews.destroy', $review))
        ->assertRedirect(route('admin.reviews.index'));

    expect($review->fresh())->toBeNull();
});
