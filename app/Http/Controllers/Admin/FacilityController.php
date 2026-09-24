<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFacilityRequest;
use App\Http\Requests\Admin\UpdateFacilityRequest;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('admin/Facilities/Index', [
            'facilities' => Facility::ordered()->paginate(15),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('admin/Facilities/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFacilityRequest $request): RedirectResponse
    {
        Facility::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Fasilitas ditambahkan.']);

        return to_route('admin.facilities.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Facility $facility): Response
    {
        return Inertia::render('admin/Facilities/Edit', [
            'facility' => $facility,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFacilityRequest $request, Facility $facility): RedirectResponse
    {
        $facility->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Fasilitas diperbarui.']);

        return to_route('admin.facilities.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Facility $facility): RedirectResponse
    {
        $facility->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Fasilitas dihapus.']);

        return to_route('admin.facilities.index');
    }
}
