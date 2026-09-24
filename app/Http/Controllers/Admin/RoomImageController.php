<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoomImageRequest;
use App\Models\RoomImage;
use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class RoomImageController extends Controller
{
    /**
     * Store newly uploaded gallery images for the given room type.
     */
    public function store(StoreRoomImageRequest $request, RoomType $roomType): RedirectResponse
    {
        $nextOrder = ($roomType->images()->max('sort_order') ?? -1) + 1;
        $hasCover = $roomType->images()->where('is_cover', true)->exists();

        foreach ($request->file('images') as $index => $file) {
            $roomType->images()->create([
                'path' => $file->store('room-images', 'public'),
                'is_cover' => ! $hasCover && $index === 0,
                'sort_order' => $nextOrder + $index,
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Gambar galeri ditambahkan.']);

        return to_route('admin.room-types.edit', $roomType);
    }

    /**
     * Mark the given image as the cover of its room type.
     */
    public function setCover(RoomImage $image): RedirectResponse
    {
        DB::transaction(function () use ($image): void {
            $image->roomType->images()->update(['is_cover' => false]);
            $image->update(['is_cover' => true]);
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Cover galeri diperbarui.']);

        return to_route('admin.room-types.edit', $image->roomType);
    }

    /**
     * Remove the given image including its stored file.
     */
    public function destroy(RoomImage $image): RedirectResponse
    {
        $roomType = $image->roomType;
        $wasCover = $image->is_cover;

        Storage::disk('public')->delete($image->path);
        $image->delete();

        if ($wasCover) {
            $roomType->images()->ordered()->first()?->update(['is_cover' => true]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Gambar galeri dihapus.']);

        return to_route('admin.room-types.edit', $roomType);
    }
}
