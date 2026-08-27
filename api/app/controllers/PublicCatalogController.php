<?php

namespace App\Controllers;

// Volontairement SANS Auth::middlware('auth') : sert la page de garde
// avant connexion (services/formations, contenu déjà public sur le site),
// contrairement à SiteCatalogController qui sert l'espace client connecté.
class PublicCatalogController
{
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

    public function digitalExpertVideos()
    {
        return response()->json([
            'success' => true,
            'data' => \siteCatalog::findDigitalExpertVideos('fr'),
        ]);
    }
}
