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

//rutas de reportes
Route::get('sales/reports_day','ReportController@reports_day')->name('reports.day');
Route::get('sales/reports_month','ReportController@reports_month')->name('reports.month');
Route::get('sales/reports_year','ReportController@reports_year')->name('reports.year');
Route::get('sales/reports_date','ReportController@reports_date')->name('reports.date');
//Para la consulta
Route::post('sales/report_results','ReportController@report_results')->name('report.results');
//fin rutas de reportes

//rutas de gestion basica de inventario
Route::resource('brands',       'BrandController')->    names('brands');
Route::resource('categories',   'CategoryController')-> names('categories');
Route::resource('clients',      'ClientController')->   names('clients');
Route::resource('products',     'ProductController')->  names('products');
Route::resource('providers',    'ProviderController')-> names('providers');
//fin rutas de gestion basica de inventario

//rutas de proceso de compra-venta con excepciones
Route::resource('purchases',    'PurchaseController'::class)-> names('purchases')->except([
    'edit','update','destroy'
]);
Route::resource('sales',        'SaleController'::class)->     names('sales')->except([
    'edit','update','destroy'
]);
//fin rutas de proceso de compra-venta con excepciones

//rutas de cambio de estados
Route::get('change_status/products/{product}','ProductController@change_status')->name('change.status.products');
Route::get('change_status/purchases/{purchase}','PurchaseController@change_status')->name('change.status.purchases');
Route::get('change_status/sales/{sale}','SaleController@change_status')->name('change.status.sales');
//fin rutas de cambio de estados

//rutas de impresión de tickets de compra y venta
Route::get('purchases/pdf/{purchase}', 'PurchaseController@pdf'::class)->     name('purchases.pdf');
Route::get('sales/pdf/{sale}', 'SaleController@pdf'::class)->     name('sales.pdf');
//fin rutas de impresión de tickets de compra y venta

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
