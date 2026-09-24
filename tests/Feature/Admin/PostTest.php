<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('guests are redirected to login', function () {
    $this->get(route('admin.posts.index'))->assertRedirect(route('login'));
});

test('non-admin users are forbidden', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.posts.index'))->assertForbidden();

    $this->actingAs($user)->post(route('admin.posts.store'), [])->assertForbidden();
});

test('admins can view the post list', function () {
    Post::factory()->count(2)->create(['is_published' => false, 'published_at' => null]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.posts.index'))
        ->assertOk();
});

test('admins can filter the post list by status', function () {
    $draft = Post::factory()->create(['is_published' => false, 'published_at' => null]);
    $published = Post::factory()->published()->create();

    $response = $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.posts.index', ['status' => 'draft']));

    $response->assertOk();

    $ids = collect($response->viewData('page')['props']['posts']['data'])->pluck('id');

    expect($ids)->toContain($draft->id)->not->toContain($published->id);
});

test('admins can create a post with an auto-generated slug and author', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.posts.store'), [
        'title' => 'Pengumuman Libur Bersama',
        'excerpt' => 'Ringkasan singkat.',
        'body' => 'Isi lengkap pengumuman.',
    ]);

    $response->assertSessionHasNoErrors()->assertRedirect(route('admin.posts.index'));

    $post = Post::where('title', 'Pengumuman Libur Bersama')->firstOrFail();

    expect($post->slug)->toBe('pengumuman-libur-bersama')
        ->and($post->author_id)->toBe($admin->id)
        ->and($post->is_published)->toBeFalse();
});

test('publishing a post stamps the publication date', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('admin.posts.store'), [
        'title' => 'Berita Terbit',
        'body' => 'Isi berita.',
        'is_published' => true,
    ])->assertSessionHasNoErrors();

    $post = Post::where('title', 'Berita Terbit')->firstOrFail();

    expect($post->is_published)->toBeTrue()
        ->and($post->published_at)->not->toBeNull();
});

test('admins can upload a cover image', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('admin.posts.store'), [
        'title' => 'Berita Bersampul',
        'body' => 'Isi berita.',
        'cover_path' => UploadedFile::fake()->image('sampul.jpg'),
    ])->assertSessionHasNoErrors();

    $post = Post::where('title', 'Berita Bersampul')->firstOrFail();

    expect($post->cover_path)->not->toBeNull();

    Storage::disk('public')->assertExists($post->cover_path);
});

test('editor html is sanitized on save', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('admin.posts.store'), [
        'title' => 'Berita Editor',
        'body' => '<p onclick="jahat()">Halo</p><script>alert(1)</script><h2>Judul</h2>',
    ])->assertSessionHasNoErrors();

    $post = Post::where('title', 'Berita Editor')->firstOrFail();

    expect($post->body)->not->toContain('<script>', 'onclick')
        ->and($post->body)->toContain('<p>Halo</p>', '<h2>Judul</h2>');
});

test('admins can preview a post', function () {
    $post = Post::factory()->published()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.posts.show', $post))
        ->assertOk();
});

test('guests cannot preview a post', function () {
    $post = Post::factory()->published()->create();

    $this->get(route('admin.posts.show', $post))->assertRedirect(route('login'));
});

test('admins can update a post and the slug follows title changes', function () {
    $post = Post::factory()->create(['is_published' => false, 'published_at' => null, 'title' => 'Judul Lama', 'slug' => 'judul-lama']);
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->put(route('admin.posts.update', $post), [
        'title' => 'Judul Baru',
        'body' => $post->body,
    ])->assertSessionHasNoErrors()->assertRedirect(route('admin.posts.index'));

    expect($post->refresh()->slug)->toBe('judul-baru');
});

test('admins can delete a post along with its cover', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('admin.posts.store'), [
        'title' => 'Berita Dihapus',
        'body' => 'Isi berita.',
        'cover_path' => UploadedFile::fake()->image('sampul.jpg'),
    ])->assertSessionHasNoErrors();

    $post = Post::where('title', 'Berita Dihapus')->firstOrFail();
    $coverPath = $post->cover_path;

    $this->actingAs($admin)
        ->delete(route('admin.posts.destroy', $post))
        ->assertRedirect(route('admin.posts.index'));

    expect($post->fresh())->toBeNull();

    Storage::disk('public')->assertMissing($coverPath);
});
