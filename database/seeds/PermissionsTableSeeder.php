<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // CATEGORÍAS
            ['name' => 'Navegación por Categorías', 'slug' => 'categories.index', 'description' => 'Listado y navegación por todas las categorías de productos en el sistema'],
            ['name' => 'Creación de Categorías', 'slug' => 'categories.create', 'description' => 'Creación de categorías de productos en el sistema'],
            ['name' => 'Visualización de detalle de Categoría', 'slug' => 'categories.show', 'description' => 'Visualización en detalle el listado de productos de cada categoría'],
            ['name' => 'Edición de Categorías', 'slug' => 'categories.edit', 'description' => 'Edición de cualquier dato de una categoría del sistema'],

            // MARCAS
            ['name' => 'Navegación por Marcas', 'slug' => 'brands.index', 'description' => 'Listado y navegación por todas las marcas de productos en el sistema'],
            ['name' => 'Creación de Marcas', 'slug' => 'brands.create', 'description' => 'Creación de marcas de productos en el sistema'],
            ['name' => 'Visualización de detalle de Marca', 'slug' => 'brands.show', 'description' => 'Visualización en detalle el listado de productos de cada marca'],
            ['name' => 'Edición de Marcas', 'slug' => 'brands.edit', 'description' => 'Edición de cualquier dato de una marca del sistema'],

            // PRODUCTOS E INVENTARIO
            ['name' => 'Navegación por Productos', 'slug' => 'products.index', 'description' => 'Listado y navegación por todos los productos y servicios en el sistema'],
            ['name' => 'Creación de Productos', 'slug' => 'products.create', 'description' => 'Creación de productos y servicios en el sistema'],
            ['name' => 'Visualización de detalle de Producto', 'slug' => 'products.show', 'description' => 'Visualización en detalle de los productos en el sistema'],
            ['name' => 'Edición de Productos', 'slug' => 'products.edit', 'description' => 'Edición de cualquier dato de un producto del sistema'],
            ['name' => 'Cambiar Estado de Producto', 'slug' => 'change.status.products', 'description' => 'Permite habilitar o deshabilitar un producto'],
            ['name' => 'Kardex y Ajustes de Inventario', 'slug' => 'kardex.index', 'description' => 'Acceso al historial auditable de Kardex y registro de ajustes/mermas de stock'],

            // PROVEEDORES
            ['name' => 'Navegación por Proveedores', 'slug' => 'providers.index', 'description' => 'Listado y navegación por todos los Proveedores en el sistema'],
            ['name' => 'Creación de Proveedores', 'slug' => 'providers.create', 'description' => 'Creación de Proveedores en el sistema'],
            ['name' => 'Visualización de detalle de Proveedor', 'slug' => 'providers.show', 'description' => 'Visualización en detalle de los Proveedores en el sistema'],
            ['name' => 'Edición de Proveedores', 'slug' => 'providers.edit', 'description' => 'Edición de cualquier dato de un Proveedor del sistema'],

            // CLIENTES
            ['name' => 'Navegación por Clientes', 'slug' => 'clients.index', 'description' => 'Listado y navegación por todos los Clientes en el sistema'],
            ['name' => 'Creación de Clientes', 'slug' => 'clients.create', 'description' => 'Creación de Clientes en el sistema'],
            ['name' => 'Visualización de detalle de Cliente', 'slug' => 'clients.show', 'description' => 'Visualización en detalle de los Clientes en el sistema'],
            ['name' => 'Edición de Clientes', 'slug' => 'clients.edit', 'description' => 'Edición de cualquier dato de un Cliente del sistema'],

            // USUARIOS Y ROLES
            ['name' => 'Navegación por Usuarios', 'slug' => 'users.index', 'description' => 'Listado y navegación por todos los usuarios registrados'],
            ['name' => 'Creación de Usuarios', 'slug' => 'users.create', 'description' => 'Creación de usuarios en el sistema'],
            ['name' => 'Visualización de detalle de Usuario', 'slug' => 'users.show', 'description' => 'Visualización en detalle de cada usuario del sistema'],
            ['name' => 'Edición de Usuarios', 'slug' => 'users.edit', 'description' => 'Edición de cualquier dato de usuarios del sistema'],
            ['name' => 'Cambiar Estado de Usuario', 'slug' => 'change.status.users', 'description' => 'Permite el cambio de estado de un usuario'],
            ['name' => 'Navegación por Roles', 'slug' => 'roles.index', 'description' => 'Listado y navegación por todos los roles del sistema'],
            ['name' => 'Creación de Roles', 'slug' => 'roles.create', 'description' => 'Acceder a la vista de Creación de roles en el sistema'],
            ['name' => 'Visualización de detalle de Rol', 'slug' => 'roles.show', 'description' => 'Visualización en detalle de roles de usuarios'],
            ['name' => 'Edición de Roles', 'slug' => 'roles.edit', 'description' => 'Edición de cualquier dato de un rol del sistema'],

            // VENTAS, POS, CRÉDITOS Y COTIZACIONES
            ['name' => 'Navegación por Ventas y Créditos', 'slug' => 'sales.index', 'description' => 'Listado y navegación por todas las ventas y cuentas por cobrar'],
            ['name' => 'Creación de Ventas (POS, Tradicional y Crédito)', 'slug' => 'sales.create', 'description' => 'Creación de ventas en cualquier modalidad'],
            ['name' => 'Visualización de detalle de Venta', 'slug' => 'sales.show', 'description' => 'Visualización en detalle de cada venta registrada'],
            ['name' => 'Cambiar Estado de Venta', 'slug' => 'change.status.sales', 'description' => 'Permite anular o confirmar una Venta'],
            ['name' => 'Generar PDF y Ticket de Venta', 'slug' => 'sales.pdf', 'description' => 'Generar y descargar notas de Venta en PDF A4 y Ticket 80mm'],
            ['name' => 'Gestión de Cotizaciones y Proformas', 'slug' => 'quotations.index', 'description' => 'Crear, imprimir y convertir cotizaciones/proformas en ventas'],

            // CAJA Y MOVIMIENTOS FINANCIEROS
            ['name' => 'Apertura, Arqueo y Cierre de Caja', 'slug' => 'cash_registers.index', 'description' => 'Gestionar turnos de caja, ingresos, gastos operativos y arqueos'],
            ['name' => 'Gestión de Formas de Pago', 'slug' => 'payment_methods.index', 'description' => 'Configurar métodos de cobro (Efectivo, QR, Transferencias, Tarjetas)'],

            // COMPRAS
            ['name' => 'Navegación por Compras', 'slug' => 'purchases.index', 'description' => 'Listado y navegación por todas las Compras registradas'],
            ['name' => 'Creación de Compras', 'slug' => 'purchases.create', 'description' => 'Creación de Compras en el sistema'],
            ['name' => 'Visualización de detalle de Compra', 'slug' => 'purchases.show', 'description' => 'Visualización en detalle de cada compra registrada'],
            ['name' => 'Cambiar Estado de Compra', 'slug' => 'change.status.purchases', 'description' => 'Permite anular o confirmar una Compra'],
            ['name' => 'Generar PDF de nota de Compra', 'slug' => 'purchases.pdf', 'description' => 'Generar y descargar notas de Compra en PDF'],

            // REPORTES, ANALÍTICA Y CONFIGURACIÓN SAAS
            ['name' => 'Acceso a Analítica y Pronóstico', 'slug' => 'analytics.index', 'description' => 'Acceder al módulo de analítica para visualizar datos históricos y pronóstico de demanda'],
            ['name' => 'Reporte de Ventas', 'slug' => 'reports.date', 'description' => 'Acceder al módulo de reportes de Ventas'],
            ['name' => 'Reporte de Compras', 'slug' => 'reportscm.datecm', 'description' => 'Acceder al módulo de reportes de Compras'],
            ['name' => 'Configuración de Empresa, Rubro y Sucursales', 'slug' => 'company.index', 'description' => 'Configurar datos del negocio, logo, moneda, rubro SaaS y sucursales'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['slug' => $perm['slug']], $perm);
        }
    }
}

if (!class_exists('PermissionsTableSeeder')) {
    class_alias(PermissionsTableSeeder::class, 'PermissionsTableSeeder');
}
