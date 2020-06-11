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

Route::resource('/orders', 'OrderController', ['orders']);
Route::resource('/links', 'LinksController', ['links']);
Route::resource('/products', 'ProductController', ['products']);
Route::get('/', 'MainPageController@index');
//Route::get('/horizon', function (){
//    return redirect();
//});
