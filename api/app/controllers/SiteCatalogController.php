<?php

namespace App\Controllers;

use App\Controllers\AuthController as Auth;

class SiteCatalogController
{
    public function __construct()
    {
        Auth::middlware('auth');
    }

    public function services()
    {
        return response()->json([
            'success' => true,
            'data' => \siteCatalog::findServices('fr'),
        ]);
    }

    public function formations()
    {
        return response()->json([
            'success' => true,
            'data' => \siteCatalog::findFormations('fr'),
        ]);
    }

    public function realisations()
    {
        return response()->json([
            'success' => true,
            'data' => \siteCatalog::findReferences('fr'),
        ]);
    }

    public function testimonials()
    {
        return response()->json([
            'success' => true,
            'data' => \siteCatalog::findTestimonials('fr'),
        ]);
    }
}
