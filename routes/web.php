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
    return view('welcome');
});

Route::group(['middleware' => ['web']], function () {
    Route::get('/home', 'HomeController@index')->name('home');

    //product core actions
    Route::resource('product', 'ProductController');

    //cart actions
    Route::resource('cart', 'CartController');
    Route::get('/cart/{id}/add', 'CartController@add')->name('add');
    Route::get('/cart/{id}/remove', 'CartController@remove')->name('remove');
    Route::get('/cart/place', 'CartController@place')->name('place');
});

Auth::routes();
