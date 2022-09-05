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

Route::get('/prueba', function () {
    return view('prueba');
});


Route::resource('brands',       'BrandController')->    names('brands');
Route::resource('categories',   'CategoryController')-> names('categories');
Route::resource('clients',      'ClientController')->   names('clients');
Route::resource('products',     'ProductController')->  names('products');
Route::resource('providers',    'ProviderController')-> names('providers');
Route::resource('purchases',    'PurchaseController')-> names('purchases');
Route::resource('sales',        'SaleController')->     names('sales');




