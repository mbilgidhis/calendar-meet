<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});

Auth::routes(['register' => config('auth.register_status')]);

Route::get('/home', 'HomeController@index')->name('home');
// Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
// Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::get('auth/google', 'GoogleController@redirectToGoogle');
Route::get('auth/google/callback', 'GoogleController@handleGoogleCallback');
Route::get('form/{id}/{eventid}', 'FormController@index')->name('form');
Route::post('form/add/{eventid}', 'FormController@add')->name('form.add');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/events', 'EventController@index')->name('events.list');
    Route::get('/events/add', 'EventController@add')->name('events.add');
    Route::post('/events/save', 'EventController@save')->name('events.save');
    Route::get('/events/edit/{id}', 'EventController@edit')->name('events.edit');
    Route::post('/events/update', 'EventController@update')->name('events.update');
    Route::get('/get-token', 'EventController@getToken')->name('get-token');

});
