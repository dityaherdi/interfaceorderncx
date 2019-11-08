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

Route::get('/', function () {
    return view('index');
});

// Route::get('order/custref', 'CheckOrderController@checkOrder')->name('order:byCustRef');
Route::match(['get', 'post'], 'order/custref', 'CheckOrderController@checkOrder')->name('order:byCustRef');

// Route::get('test', 'TestController@testConnection')->name('db:test');

Route::get('/template', function () {
    return view('template');
});

// Route::get('/', function () {
//     return view('welcome');
// });
