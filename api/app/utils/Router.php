<?php

namespace App\Utils;

use Leaf\App;
use Leaf\Http\Request;
use Leaf\Http\Response;
use  Leaf\Http\Headers as rheaders;

class Router
{



    public function loadRoutes()
    {

        app()->get('/', 'App\Controllers\FactureAPIController@index');

        app()->group('/account', function () {
            app()->post('/login', 'App\Controllers\AuthController@login');
            app()->post('/google-login', 'App\Controllers\AuthController@googleLogin');
            app()->post('/create', 'App\Controllers\AuthController@signup');
            app()->put('/edit', 'App\Controllers\AccountController@update');
            app()->post('/edit/picture', 'App\Controllers\AccountController@updatePicture');
            app()->get('/me', 'App\Controllers\AccountController@show');
            app()->post('/delete', 'App\Controllers\AccountController@delete');
        });
        app()->group('/token', function () {
            app()->post('/check', 'App\Controllers\AuthController@check');
        });
        app()->group('/notifications', function () {
            app()->post('/preferences', 'App\Controllers\NotificationController@updatePreference');
        });
        // Publiques (sans compte) : page de garde avant connexion.
        app()->get('/public/site-services', 'App\Controllers\PublicCatalogController@services');
        app()->get('/public/site-formations', 'App\Controllers\PublicCatalogController@formations');
        app()->get('/public/digital-expert-videos', 'App\Controllers\PublicCatalogController@digitalExpertVideos');
        app()->get('/expertises', 'App\Controllers\ExpertiseController@index');
        app()->get('/site-services', 'App\Controllers\SiteCatalogController@services');
        app()->get('/site-formations', 'App\Controllers\SiteCatalogController@formations');
        app()->get('/site-realisations', 'App\Controllers\SiteCatalogController@realisations');
        app()->get('/site-testimonials', 'App\Controllers\SiteCatalogController@testimonials');
        app()->get('/dashboard/payments-chart', 'App\Controllers\DashboardController@paymentsChart');
        app()->get('/factures', 'App\Controllers\FacturesController@index');
        // Pas de Auth::middlware('auth') ici : voir PdfController, le token
        // arrive en query string car ces URLs sont ouvertes hors de l'app.
        // Déclarée avant /factures/{id} : sinon ce dernier capture aussi
        // /factures/31/pdf et répond 401 (Auth::middlware côté header).
        app()->get('/factures/{id}/pdf', 'App\Controllers\PdfController@facture');
        app()->get('/factures/{id}', 'App\Controllers\FacturesController@show');
        app()->get('/devis', 'App\Controllers\DevisController@index');
        app()->get('/devis/{id}/pdf', 'App\Controllers\PdfController@devis');
        app()->get('/devis/{id}', 'App\Controllers\DevisController@show');
        app()->post('/devis/accept', 'App\Controllers\DevisController@accept');
        app()->get('/realisations', 'App\Controllers\RealisationController@index');
        app()->get('/testimonials', 'App\Controllers\TestimonialController@index');
        app()->get('/testimonials/mine', 'App\Controllers\TestimonialController@mine');
        app()->post('/testimonials', 'App\Controllers\TestimonialController@store');
        app()->get('/reclamations', 'App\Controllers\ReclamationController@index');
        app()->post('/reclamations/add', 'App\Controllers\ReclamationController@add');
        app()->get('/contact/info', 'App\Controllers\ContactController@index');
        app()->post('/contact/send', 'App\Controllers\ContactController@contact');
        app()->post('/password/code', 'App\Controllers\AccountController@sendResetPassCode');
        app()->post('/password/reset', 'App\Controllers\AccountController@resetPassword');

        app()->get('/marketing-plan', 'App\Controllers\MarketingPlanController@index');
        app()->get('/marketing-plan/{id}', 'App\Controllers\MarketingPlanController@show');
        app()->post('/marketing-plan/generate', 'App\Controllers\MarketingPlanController@generate');
        app()->post('/marketing-plan/{id}/submit', 'App\Controllers\MarketingPlanController@submit');

        app()->get('/loyalty', 'App\Controllers\LoyaltyController@index');
        app()->post('/loyalty/social-follow', 'App\Controllers\LoyaltyController@socialFollow');

        app()->get('/parrainage', 'App\Controllers\ParrainageController@index');
        app()->post('/parrainage', 'App\Controllers\ParrainageController@create');

        app()->get('/attestations', 'App\Controllers\AttestationController@index');
        app()->post('/attestations/{id}/sign', 'App\Controllers\AttestationController@sign');
        app()->get('/attestations/{id}/pdf', 'App\Controllers\AttestationController@pdf');


        // in case of 404 error
        app()->all('/{any:.*}', function () {
            return response()->json([
                'success' => false,
                'message' => 'Oops! cette action n\'est pas disponible',
            ], 404);
        });

        app()->run();
    }
}
