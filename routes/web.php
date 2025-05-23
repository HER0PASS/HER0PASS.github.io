<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/*
$router->get('/', function () use ($router) {
    return $router->app->version();
});

*/

$router->get('/', function () {
    return 'Lumen funcionando 🎉';
});

$router->post('/register', 'Register\RegisterController@register');

$router->post('/token', 'Token\TokenController@token');

$router->get('/analytics/topsofthetops', function () {
    require __DIR__ . '/../public/endpoints/analytics/topsofthetops.php';
});

$router->get('/analytics/user', [
    'middleware' => 'auth',
    'uses' => 'GetUserById\GetUserByIdController@getUser'
]);

$router->get('/analytics/streams', function () {
    require __DIR__ . '/../public/endpoints/analytics/streams.php';
});

$router->get('/analytics/streams/enriched', function () {
    require __DIR__ . '/../public/endpoints/analytics/streams/enriched.php';
});
