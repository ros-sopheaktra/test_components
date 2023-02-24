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

// this routes was created for use of the compoents [ logic mix with the use of components ]
Route::namespace('Dashboard')->group(function(){
    Route::group([
        'prefix' => '/'
    ], function(){
        Route::get('/', 'DashboardController@index');
    });
});

// this routes was created for testing each individual components
Route::namespace('Components')->group(function(){
    Route::group([
        'prefix' => '/components/demo/'
    ], function(){
        Route::get('/buttons', 'ComponentController@button');
        Route::get('/dropdowns', 'ComponentController@dropdown');
        Route::get('/tables', 'ComponentController@table');
    });
});
