<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/prueba', function () {
    return view('prueba');
});

// Rutas de Configuración de Empresa, Rubro SaaS y Multi-Sucursal
Route::get('company',                       'CompanyController@index')->name('company.index');
Route::put('company',                       'CompanyController@update')->name('company.update');
Route::post('branches',                     'CompanyController@storeBranch')->name('branches.store');
Route::put('branches/{branch}',             'CompanyController@updateBranch')->name('branches.update');
Route::post('branches/switch',              'CompanyController@switchBranch')->name('branches.switch');

// Rutas SuperAdmin SaaS (Gestión de Empresas Clientes / Multi-Tenant)
Route::get('saas/tenants',                  'CompanyController@tenants')->name('saas.tenants.index');
Route::post('saas/tenants',                 'CompanyController@storeTenant')->name('saas.tenants.store');
Route::post('saas/tenants/switch',          'CompanyController@switchTenant')->name('saas.tenants.switch');
Route::put('saas/tenants/{company}/plan',   'CompanyController@updateTenantPlan')->name('saas.tenants.plan');

// Rutas Especializadas Rubro RESTAURANTE (Salón de Mesas, Comandas a Cocina, Pre-Cuenta, Llevar y Delivery)
Route::get('restaurant',                                    'RestaurantController@index')->name('restaurant.index');
Route::post('restaurant/tables',                            'RestaurantController@storeTable')->name('restaurant.tables.store');
Route::post('restaurant/tables/{table}/open',               'RestaurantController@openTable')->name('restaurant.tables.open');
Route::post('restaurant/orders/external',                   'RestaurantController@storeExternalOrder')->name('restaurant.orders.external');
Route::get('restaurant/orders/{order}',                     'RestaurantController@showOrder')->name('restaurant.orders.show');
Route::post('restaurant/orders/{order}/items',              'RestaurantController@addItem')->name('restaurant.orders.items.add');
Route::delete('restaurant/items/{item}',                    'RestaurantController@removeItem')->name('restaurant.items.remove');
Route::post('restaurant/items/{item}/status',               'RestaurantController@updateItemStatus')->name('restaurant.items.status');
Route::get('restaurant/orders/{order}/kitchen-ticket',      'RestaurantController@kitchenTicket')->name('restaurant.orders.kitchen');
Route::get('restaurant/orders/{order}/prebill-ticket',      'RestaurantController@prebillTicket')->name('restaurant.orders.prebill');
Route::post('restaurant/orders/{order}/checkout',           'RestaurantController@checkout')->name('restaurant.orders.checkout');
Route::post('restaurant/orders/{order}/cancel',             'RestaurantController@cancelOrder')->name('restaurant.orders.cancel');

// Rutas de Control de Caja, Turnos, Arqueos y Gastos Operativos (Caja Chica)
Route::get('cash_registers',                            'CashRegisterController@index')->name('cash_registers.index');
Route::post('cash_registers/open',                      'CashRegisterController@open')->name('cash_registers.open');
Route::post('cash_registers/movements',                 'CashRegisterController@storeMovement')->name('cash_registers.movements.store');
Route::post('cash_registers/{cashRegister}/close',      'CashRegisterController@close')->name('cash_registers.close');
Route::get('cash_registers/{cashRegister}',             'CashRegisterController@show')->name('cash_registers.show');
Route::get('cash_registers/pdf/{cashRegister}',         'CashRegisterController@pdf')->name('cash_registers.pdf');

// Rutas de Kardex Auditable, Ajustes de Inventario / Mermas y Traspasos entre Sucursales
Route::get('kardex',                        'InventoryMovementController@index')->name('kardex.index');
Route::post('kardex/adjust',                'InventoryMovementController@storeAdjustment')->name('kardex.adjust');
Route::get('transfers',                     'StockTransferController@index')->name('transfers.index');
Route::post('transfers',                    'StockTransferController@store')->name('transfers.store');
Route::get('transfers/{transfer}/pdf',      'StockTransferController@pdf')->name('transfers.pdf');
Route::post('transfers/{transfer}/cancel',  'StockTransferController@cancel')->name('transfers.cancel');

// Rutas de Cotizaciones / Proformas
Route::get('quotations/pdf/{quotation}',            'QuotationController@pdf')->name('quotations.pdf');
Route::post('quotations/{quotation}/convert',       'QuotationController@convertToSale')->name('quotations.convert');
Route::resource('quotations',                       'QuotationController')->names('quotations')->only([
    'index', 'create', 'store', 'show'
]);

// Rutas de Reportes
Route::get('reportv/reports_date',          'ReportController@reports_date')->name('reports.date');
Route::get('reportv/exportar',              'ReportController@exportar')->name('reports.export');
Route::post('reportv/report_results',       'ReportController@report_results')->name('report.results');
Route::get('reportc/reportscm_datecm',      'ReportcmController@reportscm_datecm')->name('reportscm.datecm');
Route::post('reportc/reportcm_resultscm',   'ReportcmController@reportcm_resultscm')->name('reportcm.resultscm');

// Rutas de Gestión de Inventario y Directorios
Route::resource('brands',       'BrandController')->names('brands');
Route::resource('categories',   'CategoryController')->names('categories');
Route::resource('clients',      'ClientController')->names('clients');
Route::resource('products',     'ProductController')->names('products');
Route::resource('providers',    'ProviderController')->names('providers');

// Rutas para Modalidades de Venta (POS Rápido y Crédito)
Route::get('sales/pos',                             'SaleController@pos_create')->name('sales.pos');
Route::get('sales/credit/create',                   'SaleController@credit_create')->name('sales.credit.create');
Route::get('sales/credits',                         'SaleController@credits')->name('sales.credits');
Route::post('sales/installments/{installment}/pay', 'SaleController@pay_installment')->name('sales.installments.pay');

// Rutas de Proceso de Compra-Venta
Route::resource('purchases',    'PurchaseController')->names('purchases')->except([
    'edit', 'update', 'destroy'
]);
Route::resource('sales',        'SaleController')->names('sales')->except([
    'edit', 'update', 'destroy'
]);

// Rutas de Cambio de Estados
Route::get('change_status/products/{product}',      'ProductController@change_status')->name('change.status.products');
Route::get('change_status/purchases/{purchase}',    'PurchaseController@change_status')->name('change.status.purchases');
Route::get('change_status/sales/{sale}',            'SaleController@change_status')->name('change.status.sales');
Route::get('change_status/users/{user}',            'UserController@change_status')->name('change.status.users');

// Rutas de Impresión de Tickets y PDFs de Compra y Venta
Route::get('purchases/pdf/{purchase}',  'PurchaseController@pdf')->name('purchases.pdf');
Route::get('sales/pdf/{sale}',          'SaleController@pdf')->name('sales.pdf');
Route::get('sales/ticket/{sale}',       'SaleController@ticket')->name('sales.ticket');

// Módulo de Analítica
Route::resource('analytics',    'AnalyticsController')->names('analytics');

// Módulo de Usuarios y Roles
Route::resource('users',        'UserController')->names('users');
Route::resource('roles',        'RoleController')->names('roles');

// Módulo de Formas y Métodos de Pago
Route::resource('payment_methods', 'PaymentMethodController')->names('payment_methods');
Route::get('change_status/payment_methods/{payment_method}', 'PaymentMethodController@change_status')->name('change.status.payment_methods');

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
