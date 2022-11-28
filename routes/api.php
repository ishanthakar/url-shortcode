<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::post('/login', 'AuthController@login')->name("login");
    Route::get('/{hash}', 'ShortLinkController@process')->name('decodeAndRedirect');
    Route::group(['middleware' => ['authenticateUser']], function () {
        Route::post('/auth-refresh', 'AuthController@refresh')->name("refresh");
        Route::group(['prefix' => 'short-url'], function () {
            Route::post('/list', 'ShortLinkController@index')->name('ListUrl');
            Route::post('/store', 'ShortLinkController@store')->name('StoreUrl');
            Route::post('/{id}/update', 'ShortLinkController@update')->name('UpdateUrl');
            Route::get('/{id}/details', 'ShortLinkController@show')->name('ShowUrl');
            Route::get('/admin/{hash}', 'ShortLinkController@processAdmin')->name('decodeAndRedirect');
        });
    });
});   
