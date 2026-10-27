<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Company;
use App\Branch;
use App\CashRegister;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Configuración para fechas en español
        Carbon::setUTF8(true);
        Carbon::setLocale(config('app.locale'));
        setlocale(LC_ALL, 'es_MX', 'es', 'ES', 'es_MX.utf8');

        // Compartir configuración de Empresa, Sucursal activa y Caja abierta en todas las vistas
        View::composer('*', function ($view) {
            static $sharedData = null;

            if ($sharedData === null) {
                try {
                    $activeCompany = Company::current();
                    $activeBranch = Branch::current();
                    $allBranches = Branch::where('status', 'ACTIVO')->orderBy('is_main', 'DESC')->orderBy('name', 'ASC')->get();
                    $openCashRegister = CashRegister::activeOpen();
                } catch (\Throwable $e) {
                    $activeCompany = new Company([
                        'name' => 'SISTEMA POS & COMERCIAL',
                        'legal_name' => 'IMDICORP S.R.L.',
                        'slogan' => 'Soluciones Comerciales Multi-Rubro',
                        'nit' => '1023847029',
                        'authorization_number' => '4920018471',
                        'business_type' => 'GENERAL',
                        'email' => 'ventas@possystem.bo',
                        'phone' => '(+591) 4-4589210 / 71234567',
                        'address' => 'Av. Heroínas #742 esq. Ayacucho, Zona Central',
                        'city' => 'Cochabamba',
                        'country' => 'Bolivia',
                        'currency_code' => 'BOB',
                        'currency_symbol' => 'Bs.',
                        'currency_name' => 'Bolivianos',
                        'tax_name' => 'IVA',
                        'tax_rate' => 13.00,
                        'printer_width' => '80mm',
                        'receipt_footer' => '¡Gracias por su preferencia y confianza!',
                        'terms_conditions' => 'Documento válido de control interno.',
                        'default_stock_alert' => 10,
                    ]);
                    $activeBranch = new Branch([
                        'code' => 'SUC-01',
                        'name' => 'Casa Matriz - Central',
                        'address' => 'Av. Heroínas #742 esq. Ayacucho',
                        'city' => 'Cochabamba',
                        'phone' => '(+591) 4-4589210',
                        'is_main' => true,
                        'status' => 'ACTIVO',
                    ]);
                    $allBranches = collect([$activeBranch]);
                    $openCashRegister = null;
                }

                $sharedData = compact('activeCompany', 'activeBranch', 'allBranches', 'openCashRegister');
            }

            $view->with($sharedData);
        });
    }
}
