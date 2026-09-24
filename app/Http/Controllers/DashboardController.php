<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Review;
use App\Models\RoomType;
use App\Models\WaClick;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with WhatsApp click statistics.
     */
    public function index(): Response
    {
        $now = now();

        $month = (int) request()->query('month', $now->month);
        $month = max(1, min(12, $month));

        $year = (int) request()->query('year', $now->year);
        $year = max(2020, min($now->year + 1, $year));

        $start = Carbon::create($year, $month, 1)->startOfDay();
        $daysInMonth = $start->daysInMonth;

        $clicks = WaClick::with('roomType:id,name')
            ->whereBetween('created_at', [$start, $start->copy()->endOfMonth()])
            ->get();

        $counts = $clicks->groupBy(fn (WaClick $click): int => $click->created_at->day)
            ->map(fn ($dayClicks): int => $dayClicks->count());

        $daily = collect(range(1, $daysInMonth))->map(fn (int $day): array => [
            'day' => $day,
            'total' => $counts[$day] ?? 0,
        ])->all();

        $byRoomType = $clicks
            ->groupBy(fn (WaClick $click): string => $click->roomType?->name ?? 'Umum')
            ->map(fn ($clicks, string $name): array => [
                'name' => $name,
                'total' => $clicks->count(),
            ])
            ->sortByDesc('total')
            ->values()
            ->all();

        return Inertia::render('Dashboard', [
            'filter' => ['month' => $month, 'year' => $year],
            'stats' => [
                'wa_clicks' => array_sum(array_column($daily, 'total')),
                'room_types' => RoomType::count(),
                'reviews' => Review::count(),
            ],
            'published_posts' => Post::where('is_published', true)->count(),
            'daily' => $daily,
            'byRoomType' => $byRoomType,
        ]);
    }
}
