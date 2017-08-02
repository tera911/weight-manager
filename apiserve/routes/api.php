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

Route::options('/{all}', function(){
    return new Response('', 204);
});

Route::group(['middleware' => ['auth']], function(){
    Route::get('/user', 'Api\UserController@get');

    Route::post('/user', 'Api\UserController@update');
    Route::get('/dashboard', 'Api\UserController@dashboard');
    Route::post('/weight', 'Api\UserController@addWeight');
});

Route::get('/login', 'Api\OauthLoginController@login')->name('login');
Route::get('/cb', 'Api\OauthLoginController@callback');

Route::get('/logout', function(){
    \Auth::logout();
    return redirect('http://w.tera.jp');
});