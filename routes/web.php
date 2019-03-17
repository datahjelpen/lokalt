<?php

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

Route::get('/', 'SiteController@index')->name('index');

Auth::routes(['verify' => true]);
Route::get('epost-bekreftet', 'Auth\VerificationController@complete');

Route::get('konto', 'UserController@show')->name('user.show');
Route::post('konto', 'UserController@update')->name('user.update');

Route::get('kontrollpanel', 'DashboardController@index')->name('dashboard.index');

Route::prefix('steder')->group(function () {
    Route::get('/', 'PlaceController@index')->name('places.index');
    Route::get('opprett', 'PlaceController@create')->name('places.create');
    Route::post('lagre', 'PlaceController@store')->name('places.store');
    Route::get('{place_slug}', 'PlaceController@show')->name('places.show');
    Route::get('{place}/endre', 'PlaceController@edit')->name('places.edit');
    Route::patch('{place}/oppdater', 'PlaceController@update')->name('places.update');
    Route::get('{place}/fjern', 'PlaceController@delete')->name('places.delete');
    Route::delete('{place}/slett', 'PlaceController@destroy')->name('places.destroy');

    Route::get('{place}/apningstider', 'PlaceController@opening_hours');
    Route::get('{place}/åpningstider', 'PlaceController@opening_hours');
});
