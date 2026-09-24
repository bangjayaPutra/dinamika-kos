<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    /**
     * Display a listing of the reviews.
     */
    public function index(): Response
    {
        $reviews = Review::with('roomType:id,name')
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/Reviews/Index', [
            'reviews' => $reviews,
        ]);
    }

    /**
     * Remove the given review.
     */
    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Ulasan dihapus.']);

        return to_route('admin.reviews.index', request()->only('page'));
    }
}
