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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('findUsers', 'HomeController@findUsers');
Route::get('userData', 'SettingController@userData');
Route::post('registerUser', 'Auth\RegisterController@register');
Route::post('leftswipe', 'SwipeController@left');
Route::post('rightswipe', 'SwipeController@right');
Route::post('saveData', 'SettingController@saveData');
Route::get('matches', 'MatchController@matches');