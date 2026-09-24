<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoomTypeRequest;
use App\Http\Requests\Admin\UpdateRoomTypeRequest;
use App\Models\Facility;
use App\Models\RoomImage;
use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('admin/RoomTypes/Index', [
            'roomTypes' => RoomType::ordered()->paginate(15),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('admin/RoomTypes/Create', [
            'facilities' => Facility::byScope('kamar')->ordered()->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomTypeRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = $this->uniqueSlug($validated['name']);

        $facilityIds = $validated['facilities'] ?? [];
        unset($validated['facilities']);

        $roomType = RoomType::create($validated);
        $roomType->facilities()->sync($facilityIds);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Tipe kamar ditambahkan.']);

        return to_route('admin.room-types.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RoomType $roomType): Response
    {
        return Inertia::render('admin/RoomTypes/Edit', [
            'roomType' => $roomType,
            'facilities' => Facility::byScope('kamar')->ordered()->get(['id', 'name']),
            'selectedFacilities' => $roomType->facilities()->pluck('facilities.id')->all(),
            'images' => $roomType->images()->ordered()->get()->map(fn (RoomImage $image): array => [
                'id' => $image->id,
                'url' => Storage::url($image->path),
                'is_cover' => $image->is_cover,
            ]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomTypeRequest $request, RoomType $roomType): RedirectResponse
    {
        $validated = $request->validated();

        if ($validated['name'] !== $roomType->name) {
            $validated['slug'] = $this->uniqueSlug($validated['name'], $roomType->id);
        }

        $facilityIds = $validated['facilities'] ?? [];
        unset($validated['facilities']);

        $roomType->update($validated);
        $roomType->facilities()->sync($facilityIds);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Tipe kamar diperbarui.']);

        return to_route('admin.room-types.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoomType $roomType): RedirectResponse
    {
        $roomType->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Tipe kamar dihapus.']);

        return to_route('admin.room-types.index');
    }

    /**
     * Generate a slug that does not collide with existing room types.
     */
    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (RoomType::where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
