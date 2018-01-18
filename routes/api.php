<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('register','UserController@register');

Route::get('login','UserController@login');

Route::get('sendsms','UserController@sendSms');

Route::get('yingyanSearch','MapController@yingyanSearch');


Route::get('testOneApi','TestApiController@one');


