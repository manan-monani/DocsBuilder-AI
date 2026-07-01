<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $projects = \App\Models\DocProject::where('is_active', true)
            ->with('defaultVersion')
            ->get();

        return Inertia::render('Guest/Pages/Home', [
            'projects' => $projects,
        ]);
    }
}
