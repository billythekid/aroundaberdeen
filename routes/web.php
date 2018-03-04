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
    $names = [];
    foreach (['index', 'store', 'create', 'destroy', 'update', 'show', 'edit'] as $route) {
      $names[$route] = "subdomain." . $route;
    }
    Route::resource('/', 'SubdomainController', [$subdomain])->names($names);
  });


  Route::domain(env('APP_DOMAIN'))->group(function () {

    Route::get('/', function () {
      return view('welcome')->withSites(Site::all());
    })->name('index');

    Auth::routes();

    Route::group(['middleware' => ['auth']], function () {
      Route::resource('/site', 'SiteController');

      Route::resource('/map', 'MapController');

      Route::resource('/point','PointController');

    });
  });
