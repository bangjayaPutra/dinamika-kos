<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the room types.
     */
    public function index(): Response
    {
        $roomTypes = RoomType::with(['images' => fn ($query) => $query->ordered()])
            ->ordered()->get()->map(fn (RoomType $roomType): array => [
                'id' => $roomType->id,
                'name' => $roomType->name,
                'slug' => $roomType->slug,
                'price_monthly' => $roomType->price_monthly,
                'capacity' => $roomType->capacity,
                'size_label' => $roomType->size_label,
                'is_available' => $roomType->is_available,
                'cover_url' => ($cover = $roomType->images->first()) ? Storage::url($cover->path) : null,
            ]);

        return Inertia::render('public/RoomTypes/Index', [
            'roomTypes' => $roomTypes,
        ]);
    }

    /**
     * Display the specified room type with gallery, facilities, and reviews.
     */
    public function show(string $slug): Response
    {
        $roomType = RoomType::where('slug', $slug)->firstOrFail();

        return Inertia::render('public/RoomTypes/Show', [
            'roomType' => $roomType,
            'images' => $roomType->images()->ordered()->get()->map(fn ($image): array => [
                'id' => $image->id,
                'url' => Storage::url($image->path),
                'is_cover' => $image->is_cover,
            ]),
            'facilities' => $roomType->facilities()->ordered()->get(['facilities.id', 'facilities.name', 'facilities.icon', 'facilities.description']),
            'reviews' => $roomType->reviews()->ordered()->get(['id', 'nama', 'rating', 'body']),
        ]);
    }
}
