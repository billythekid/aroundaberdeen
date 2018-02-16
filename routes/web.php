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

  use App\Http\Controllers\SiteController;
  use App\Site;

  Route::domain('{subdomain}.' . env('APP_DOMAIN'))->group(function ($subdomain) {
    Route::get('/', function( $subdomain){
      return view('subdomain.index')
        ->withSite($subdomain);
    })->name('subdomain.index');
    Route::resource('/map', 'MapController');
    Route::resource('/point', 'PointController');
  });

  Route::group(['/', function () {
    Route::get('/', function () {
      return view('welcome');
    })->name('index');

    Auth::routes();

    Route::resource('/site', 'SiteController');
    Route::get('/home', 'HomeController@index')->name('home');
  }]);
