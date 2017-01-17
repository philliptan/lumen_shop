<?php

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

// Home page
$app->get('/', function () use ($app) {
    return $app->version();
});

$app->get('users/{id}', [
    'as'   => 'user.show', 
    'uses' => 'UsersController@show'
]);

$app->get('users', [
    'as'   => 'user.index',
    'uses' => 'UsersController@index'
]);

$app->post('users', [
    'as'   => 'user.store',
    'uses' => 'UsersController@store'
]);

/** Authorized user **/
$app->group(['middleware' => 'auth'], function () use ($app) {

    

});

