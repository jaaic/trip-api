<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/**  @var $router \Laravel\Lumen\Routing\Router */
$router->group(['namespace' => 'App\Modules\Activities\Controllers'], function () use ($router) {

    $router->get('/planner', ['uses' => 'PlannerController@getActivities']);

});