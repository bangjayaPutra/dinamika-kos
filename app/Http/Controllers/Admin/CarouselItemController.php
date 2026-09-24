<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarouselItemRequest;
use App\Http\Requests\Admin\UpdateCarouselItemRequest;
use App\Models\CarouselItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CarouselItemController extends Controller
{
    /**
     * Display a listing of the carousel items.
     */
    public function index(): Response
    {
        return Inertia::render('admin/Carousel/Index', [
            'items' => CarouselItem::ordered()->get()->map(fn (CarouselItem $item): array => [
                'id' => $item->id,
                'title' => $item->title,
                'subtitle' => $item->subtitle,
                'image_url' => Storage::url($item->image_path),
                'link_url' => $item->link_url,
                'sort_order' => $item->sort_order,
                'is_active' => $item->is_active,
            ]),
        ]);
    }

    /**
     * Show the form for creating a new carousel item.
     */
    public function create(): Response
    {
        return Inertia::render('admin/Carousel/Create');
    }

    /**
     * Store a newly created carousel item in storage.
     */
    public function store(StoreCarouselItemRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $imagePath = $validated['image'];
        unset($validated['image']);

        $validated['image_path'] = $imagePath->store('carousel', 'public');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $validated['is_active'] ?? false;

        CarouselItem::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Slide carousel ditambahkan.']);

        return to_route('admin.carousel-items.index');
    }

    /**
     * Show the form for editing the specified carousel item.
     */
    public function edit(CarouselItem $carouselItem): Response
    {
        return Inertia::render('admin/Carousel/Edit', [
            'item' => [
                'id' => $carouselItem->id,
                'title' => $carouselItem->title,
                'subtitle' => $carouselItem->subtitle,
                'image_url' => Storage::url($carouselItem->image_path),
                'link_url' => $carouselItem->link_url,
                'sort_order' => $carouselItem->sort_order,
                'is_active' => $carouselItem->is_active,
            ],
        ]);
    }

    /**
     * Update the specified carousel item in storage.
     */
    public function update(UpdateCarouselItemRequest $request, CarouselItem $carouselItem): RedirectResponse
    {
        $validated = $request->validated();

        unset($validated['image']);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($carouselItem->image_path);
            $validated['image_path'] = $request->file('image')->store('carousel', 'public');
        }

        $validated['sort_order'] = $validated['sort_order'] ?? $carouselItem->sort_order;
        $validated['is_active'] = $validated['is_active'] ?? false;

        $carouselItem->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Slide carousel diperbarui.']);

        return to_route('admin.carousel-items.index');
    }

    /**
     * Remove the specified carousel item from storage.
     */
    public function destroy(CarouselItem $carouselItem): RedirectResponse
    {
        Storage::disk('public')->delete($carouselItem->image_path);
        $carouselItem->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Slide carousel dihapus.']);

        return to_route('admin.carousel-items.index');
    }
}
