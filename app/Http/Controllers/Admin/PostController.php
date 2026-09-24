<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePostRequest;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the posts, optionally filtered by status.
     */
    public function index(): Response
    {
        $status = request()->string('status')->toString();
        $status = in_array($status, ['published', 'draft'], true) ? $status : 'all';

        $posts = Post::with('author:id,name')
            ->when($status === 'published', fn ($query) => $query->where('is_published', true))
            ->when($status === 'draft', fn ($query) => $query->where('is_published', false))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/Posts/Index', [
            'posts' => $posts,
            'status' => $status,
            'counts' => [
                'all' => Post::count(),
                'published' => Post::where('is_published', true)->count(),
                'draft' => Post::where('is_published', false)->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('admin/Posts/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = $this->uniqueSlug($validated['title']);
        $validated['author_id'] = $request->user()->id;
        $validated['is_published'] = $validated['is_published'] ?? false;

        if ($validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        unset($validated['cover_path']);

        if ($request->hasFile('cover_path')) {
            $validated['cover_path'] = $request->file('cover_path')->store('posts', 'public');
        }

        $validated['body'] = $this->cleanHtml($validated['body']);

        Post::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Post ditambahkan.']);

        return to_route('admin.posts.index');
    }

    /**
     * Preview the specified post as it will appear when published.
     */
    public function show(Post $post): Response
    {
        $post->load('author:id,name');

        return Inertia::render('admin/Posts/Show', [
            'post' => $post,
            'cover_url' => $post->cover_path ? Storage::url($post->cover_path) : null,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post): Response
    {
        return Inertia::render('admin/Posts/Edit', [
            'post' => $post,
            'cover_url' => $post->cover_path ? Storage::url($post->cover_path) : null,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $validated = $request->validated();

        if ($validated['title'] !== $post->title) {
            $validated['slug'] = $this->uniqueSlug($validated['title'], $post->id);
        }

        $validated['is_published'] = $validated['is_published'] ?? false;

        if ($validated['is_published'] && empty($validated['published_at']) && $post->published_at === null) {
            $validated['published_at'] = now();
        }

        unset($validated['cover_path']);

        if ($request->hasFile('cover_path')) {
            if ($post->cover_path) {
                Storage::disk('public')->delete($post->cover_path);
            }

            $validated['cover_path'] = $request->file('cover_path')->store('posts', 'public');
        }

        $validated['body'] = $this->cleanHtml($validated['body']);

        $post->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Post diperbarui.']);

        return to_route('admin.posts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        if ($post->cover_path) {
            Storage::disk('public')->delete($post->cover_path);
        }

        $post->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Post dihapus.']);

        return to_route('admin.posts.index');
    }

    /**
     * Strip disallowed tags and dangerous attributes from editor HTML.
     */
    protected function cleanHtml(string $html): string
    {
        $clean = strip_tags($html, '<p><br><b><strong><i><em><u><s><h2><h3><ul><ol><li><blockquote><a><code><pre><hr>');
        $clean = preg_replace('/\son\w+="[^"]*"/i', '', $clean) ?? $clean;
        $clean = preg_replace("/\son\w+='[^']*'/i", '', $clean) ?? $clean;
        $clean = preg_replace('/href="javascript:[^"]*"/i', 'href="#"', $clean) ?? $clean;

        return $clean;
    }

    /**
     * Generate a slug that does not collide with existing posts.
     */
    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (Post::where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
