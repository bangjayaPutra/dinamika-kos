<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the published posts.
     */
    public function index(): Response
    {
        $posts = Post::with('author:id,name')->published()->ordered()->paginate(9)->withQueryString();

        $posts->getCollection()->transform(fn (Post $post): array => [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'published_at' => $post->published_at,
            'author_name' => $post->author?->name,
            'cover_url' => $post->cover_path ? Storage::url($post->cover_path) : null,
        ]);

        return Inertia::render('public/Posts/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Display the specified published post.
     */
    public function show(string $slug): Response
    {
        $post = Post::with('author:id,name')->published()->where('slug', $slug)->firstOrFail();

        $latest = Post::published()->ordered()->where('id', '!=', $post->id)->limit(3)->get(['id', 'title', 'slug']);

        return Inertia::render('public/Posts/Show', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'excerpt' => $post->excerpt,
                'body' => $post->body,
                'published_at' => $post->published_at,
                'author_name' => $post->author?->name,
                'cover_url' => $post->cover_path ? Storage::url($post->cover_path) : null,
            ],
            'latest' => $latest,
        ]);
    }
}
