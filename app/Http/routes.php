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

$app->get('users/profile', [
    'as' => 'profile', 'uses' => 'UsersController@profile'
]);

$app->get('users/get_list', [
    'uses' => 'UsersController@getList'
]);

/** Authorized user **/
$app->group(['middleware' => 'auth'], function () use ($app) {

    

});

