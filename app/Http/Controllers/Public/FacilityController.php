<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Inertia\Inertia;
use Inertia\Response;

class FacilityController extends Controller
{
    /**
     * Display all facilities grouped by scope.
     */
    public function index(): Response
    {
        return Inertia::render('public/Facilities/Index', [
            'roomFacilities' => Facility::byScope('kamar')->ordered()->get(['id', 'name', 'icon', 'description']),
            'generalFacilities' => Facility::byScope('umum')->ordered()->get(['id', 'name', 'icon', 'description']),
        ]);
    }
}
