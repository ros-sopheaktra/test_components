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

Route::namespace('Dashboard')->group(function(){
    // route
    Route::get('/', 'DashboardController@index');
    Route::get('/input','DashboardController@create')->name('input-test');
});