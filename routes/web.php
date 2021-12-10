<?php

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

use App\Domain\Services\Logger;

$router->get('login', 'UserController@login');
$router->get('dashboard', 'UserController@dashboard');
$router->post('useriu', 'UserController@useriu');

$router->group(['middleware' => 'auth'], function() use ($router) {
    $router->get('gamestreams', 'UserController@gameStreams');
    $router->get('topgames', 'UserController@getTopGames');
    $router->get('topstreams[/{order}]', 'UserController@gameTopStreams');
    $router->get('datestreams[/{pages}]', 'UserController@getDateStreams');
    $router->get('followed', 'UserController@getTopFollowedStreams');
    $router->get('lowest', 'UserController@getLowestFollowedStream');
    $router->get('tags', 'UserController@getTopFollowedTags');
});


$router->get('/', function (\Illuminate\Http\Request $request) use ($router) {
    return $router->app->version();
});
