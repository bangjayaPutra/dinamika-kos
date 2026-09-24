<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    /**
     * Display the contact page. Details come from shared site settings.
     */
    public function index(): Response
    {
        return Inertia::render('public/Contact');
    }
}
