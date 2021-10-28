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

Auth::routes(['verify' => true, 'register' => false]);
Route::get('epost-bekreftet', 'Auth\VerificationController@complete');

Route::get('konto', 'UserController@show')->name('user.show');
Route::post('konto', 'UserController@update')->name('user.update');

Route::get('kontrollpanel', 'DashboardController@index')->name('dashboard.index');

Route::get('admin/kontrollpanel', 'AdminController@index')->name('admin.index');
Route::post('admin/steder/sok', 'AdminController@search_place')->name('admin.places.search');
Route::post('admin/steder/søk', 'AdminController@search_place');
Route::get('admin/steder/{place}/endre', 'AdminController@edit_place')->name('admin.places.edit');
Route::patch('admin/steder/{place}/oppdater', 'AdminController@update_place')->name('admin.places.update');
Route::get('admin/brukere/opprett', 'AdminController@create_user')->name('admin.users.create');
Route::post('admin/brukere/lagre', 'AdminController@store_user')->name('admin.users.store');

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
});
