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

Route::resource('purchases',    'PurchaseController'::class)-> names('purchases')->except([
    'edit','update','destroy'
]);
Route::resource('sales',        'SaleController'::class)->     names('sales')->except([
    'edit','update','destroy'
]);

//rutas de impresión de tickets de compra y venta
Route::get('purchases/pdf/{purchase}', 'PurchaseController@pdf'::class)->     name('purchases.pdf');
Route::get('sales/pdf/{sale}', 'SaleController@pdf'::class)->     name('sales.pdf');
//fin

//rutas de cambio de estados
Route::get('change_status/products/{product}','ProductController@change_status')->name('change.status.products');
Route::get('change_status/purchases/{purchase}','PurchaseController@change_status')->name('change.status.purchases');
Route::get('change_status/sales/{sale}','SaleController@change_status')->name('change.status.sales');
//fin


//--
//Route::get('/','CategoryController@index');
//Route::post('/categories/getCategories/','CategoryController@getCategories')->name('categories.getCategories');
//--





Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
