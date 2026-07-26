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
            app()->post('/create', 'App\Controllers\AuthController@signup');
            app()->put('/edit', 'App\Controllers\AccountController@update');
            app()->post('/edit/picture', 'App\Controllers\AccountController@updatePicture');
            app()->get('/me', 'App\Controllers\AccountController@show');
        });
        app()->group('/token', function () {
            app()->post('/check', 'App\Controllers\AuthController@check');
        });
        app()->get('/expertises', 'App\Controllers\ExpertiseController@index');
        app()->get('/factures', 'App\Controllers\FacturesController@index');
        app()->get('/factures/{id}', 'App\Controllers\FacturesController@show');
        app()->get('/devis', 'App\Controllers\DevisController@index');
        app()->get('/devis/{id}', 'App\Controllers\DevisController@show');
        app()->post('/devis/accept', 'App\Controllers\DevisController@accept');
        app()->get('/realisations', 'App\Controllers\RealisationController@index');
        app()->get('/testimonials', 'App\Controllers\TestimonialController@index');
        app()->post('/reclamations/add', 'App\Controllers\ReclamationController@add');
        app()->get('/contact/info', 'App\Controllers\ContactController@index');
        app()->post('/contact/send', 'App\Controllers\ContactController@contact');
        app()->post('/password/code', 'App\Controllers\AccountController@sendResetPassCode');
        app()->post('/password/reset', 'App\Controllers\AccountController@resetPassword');


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
