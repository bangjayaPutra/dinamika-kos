<?php

use App\Models\User;

test('guests are redirected to login', function () {
    $this->get(route('admin.users.index'))->assertRedirect(route('login'));
});

test('non-admin users are forbidden', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();

    $this->actingAs($user)->post(route('admin.users.store'), [])->assertForbidden();
});

test('admins can view the user list', function () {
    User::factory()->count(2)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.users.index'))
        ->assertOk();
});

test('admins can create an admin account that can log in immediately', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.users.store'), [
            'name' => 'Admin Baru',
            'email' => 'admin.baru@example.com',
            'role' => 'admin',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.users.index'));

    $user = User::where('email', 'admin.baru@example.com')->firstOrFail();

    expect($user->isAdmin())->toBeTrue();

    $this->post('/logout')->assertRedirect();

    $this->post('/login', [
        'email' => 'admin.baru@example.com',
        'password' => 'password',
    ])->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($user);
});

test('emails must be unique', function () {
    $existing = User::factory()->create(['email' => 'sama@example.com']);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.users.store'), [
            'name' => 'Duplikat',
            'email' => 'sama@example.com',
            'role' => 'user',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
        ->assertSessionHasErrors('email');

    expect(User::where('email', 'sama@example.com')->count())->toBe(1);
});

test('admins can update a user without changing the password', function () {
    $user = User::factory()->create(['name' => 'Lama']);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.users.update', $user), [
            'name' => 'Baru',
            'email' => $user->email,
            'role' => 'admin',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.users.index'));

    expect($user->refresh()->name)->toBe('Baru')
        ->and($user->refresh()->isAdmin())->toBeTrue();
});

test('admins cannot change their own role', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'user',
        ])
        ->assertRedirect();

    expect($admin->refresh()->isAdmin())->toBeTrue();
});

test('admins cannot delete their own account', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $admin))
        ->assertRedirect();

    expect($admin->fresh())->not->toBeNull();
});

test('admins can delete another account', function () {
    $user = User::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'));

    expect($user->fresh())->toBeNull();
});
