<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\PaymentMethod;

class PaymentMethodsTableSeeder extends Seeder
{
    public function run()
    {
        $methods = [
            [
                'name' => 'Efectivo (Bs.)',
                'code' => 'EFECTIVO',
                'payment_condition' => 'CONTADO',
                'description' => 'Pago directo en efectivo en moneda nacional (Bolivianos). Calcula cambio/vuelto automáticamente.',
                'instructions' => 'Cobrar el importe exacto o registrar el monto recibido para cálculo de vuelto.',
                'requires_reference' => false,
                'icon' => 'fas fa-money-bill-wave',
                'color' => '#10b981',
                'status' => 'ACTIVO',
                'sort_order' => 1,
            ],
            [
                'name' => 'Código QR Simple',
                'code' => 'QR',
                'payment_condition' => 'CONTADO',
                'description' => 'Pago digital instantáneo interbancario mediante código QR.',
                'instructions' => 'Verificar la notificación bancaria de recepción de fondos antes de entregar productos.',
                'requires_reference' => true,
                'icon' => 'fas fa-qrcode',
                'color' => '#6366f1',
                'status' => 'ACTIVO',
                'sort_order' => 2,
            ],
            [
                'name' => 'Transferencia Bancaria',
                'code' => 'TRANSFERENCIA',
                'payment_condition' => 'CONTADO',
                'description' => 'Depósito o transferencia electrónica directa a cuentas de la empresa.',
                'instructions' => 'Solicitar número de operación bancaria o comprobante de depósito.',
                'requires_reference' => true,
                'icon' => 'fas fa-university',
                'color' => '#0284c7',
                'status' => 'ACTIVO',
                'sort_order' => 3,
            ],
            [
                'name' => 'Tarjeta de Débito',
                'code' => 'TARJETA_DEBITO',
                'payment_condition' => 'CONTADO',
                'description' => 'Cobro mediante terminal POS físico con tarjeta de débito bancaria.',
                'instructions' => 'Ingresar número de voucher o lote del punto de venta POS.',
                'requires_reference' => true,
                'icon' => 'fas fa-credit-card',
                'color' => '#2563eb',
                'status' => 'ACTIVO',
                'sort_order' => 4,
            ],
            [
                'name' => 'Tarjeta de Crédito',
                'code' => 'TARJETA_CREDITO',
                'payment_condition' => 'AMBOS',
                'description' => 'Cobro con tarjeta de crédito bancaria con opción a diferido o contado.',
                'instructions' => 'Registrar el código de autorización emitido por el datáfono.',
                'requires_reference' => true,
                'icon' => 'far fa-credit-card',
                'color' => '#8b5cf6',
                'status' => 'ACTIVO',
                'sort_order' => 5,
            ],
            [
                'name' => 'Crédito Directo en Tienda',
                'code' => 'CREDITO_DIRECTO',
                'payment_condition' => 'CREDITO',
                'description' => 'Financiamiento directo otorgado por el negocio con cronograma de pagos en cuotas.',
                'instructions' => 'Requiere registro de cliente verificado y definición de cuota inicial y plazos.',
                'requires_reference' => false,
                'icon' => 'fas fa-hand-holding-usd',
                'color' => '#f59e0b',
                'status' => 'ACTIVO',
                'sort_order' => 6,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}
