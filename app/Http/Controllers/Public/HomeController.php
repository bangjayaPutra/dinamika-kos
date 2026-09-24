<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CarouselItem;
use App\Models\Facility;
use App\Models\Post;
use App\Models\Review;
use App\Models\RoomType;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the public homepage.
     */
    public function index(): Response
    {
        $roomTypes = RoomType::with(['images' => fn ($query) => $query->ordered()])
            ->ordered()->get()->map(fn (RoomType $roomType): array => [
                'id' => $roomType->id,
                'name' => $roomType->name,
                'slug' => $roomType->slug,
                'price_monthly' => $roomType->price_monthly,
                'cover_url' => ($cover = $roomType->images->first()) ? Storage::url($cover->path) : null,
            ]);

        return Inertia::render('public/Home', [
            'carousel' => CarouselItem::active()->ordered()->get()->map(fn (CarouselItem $item): array => [
                'id' => $item->id,
                'title' => $item->title,
                'subtitle' => $item->subtitle,
                'image_url' => Storage::url($item->image_path),
                'link_url' => $item->link_url,
            ]),
            'roomTypes' => $roomTypes,
            'facilities' => Facility::byScope('umum')->ordered()->get(['id', 'name', 'icon', 'description']),
            'reviews' => Review::ordered()->limit(6)->get(['id', 'nama', 'rating', 'body']),
            'posts' => Post::published()->ordered()->limit(3)->get(['id', 'title', 'slug', 'excerpt', 'published_at']),
        ]);
    }
}
