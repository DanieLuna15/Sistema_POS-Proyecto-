<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Caffeinated\Shinobi\Models\Role;

class Company extends Model
{
    protected $fillable = [
        'owner_user_id',
        'name',
        'legal_name',
        'slogan',
        'nit',
        'authorization_number',
        'business_type',
        'plan_name',
        'subscription_ends_at',
        'max_users',
        'max_branches',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'country',
        'logo',
        'currency_code',
        'currency_symbol',
        'currency_name',
        'tax_name',
        'tax_rate',
        'printer_width',
        'receipt_footer',
        'terms_conditions',
        'default_stock_alert',
        'enable_pos',
        'enable_credits',
        'enable_quotations',
        'enable_cash_register',
        'enable_kardex',
        'enable_lots_expiry',
        'enable_serials_warranties',
        'enable_services',
        'enable_wholesale',
        'status',
    ];

    protected $casts = [
        'tax_rate' => 'float',
        'default_stock_alert' => 'integer',
        'max_users' => 'integer',
        'max_branches' => 'integer',
        'enable_pos' => 'boolean',
        'enable_credits' => 'boolean',
        'enable_quotations' => 'boolean',
        'enable_cash_register' => 'boolean',
        'enable_kardex' => 'boolean',
        'enable_lots_expiry' => 'boolean',
        'enable_serials_warranties' => 'boolean',
        'enable_services' => 'boolean',
        'enable_wholesale' => 'boolean',
    ];

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    /**
     * Devuelve el ID de la empresa activa según el usuario autenticado (Aislamiento Multi-Tenant).
     */
    public static function currentId()
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                // Si es SuperAdmin SaaS y seleccionó administrar una empresa específica en sesión
                if ($user->is_superadmin && session()->has('active_company_id')) {
                    return intval(session('active_company_id'));
                }
                if ($user->company_id) {
                    return intval($user->company_id);
                }
            }
            return 1;
        } catch (\Throwable $e) {
            return 1;
        }
    }

    /**
     * Obtiene la instancia de la empresa activa del cliente logueado (o crea la base inicial).
     */
    public static function current()
    {
        try {
            $targetId = self::currentId();
            $company = self::find($targetId) ?? self::first();

            if (!$company) {
                $company = self::create([
                    'name' => 'SISTEMA POS & COMERCIAL',
                    'legal_name' => 'IMDICORP S.R.L.',
                    'slogan' => 'Venta y Distribución de Productos y Servicios Multi-Rubro',
                    'nit' => '1023847029',
                    'authorization_number' => '4920018471',
                    'business_type' => 'GENERAL',
                    'plan_name' => 'EMPRESARIAL',
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
                    'receipt_footer' => '¡Gracias por su preferencia y confianza! Conserve este comprobante para cualquier reclamo o garantía.',
                    'terms_conditions' => 'Todo cambio o reclamo debe efectuarse dentro de las 48 horas presentando este comprobante.',
                    'default_stock_alert' => 10,
                ]);

                Branch::withoutGlobalScopes()->firstOrCreate(
                    ['company_id' => $company->id, 'code' => 'SUC-01'],
                    [
                        'name' => 'Casa Matriz - Central',
                        'manager_name' => 'Administración General',
                        'phone' => $company->phone,
                        'email' => $company->email,
                        'address' => $company->address,
                        'city' => $company->city,
                        'authorization_number' => $company->authorization_number,
                        'is_main' => true,
                        'status' => 'ACTIVO',
                    ]
                );
            }
            return $company;
        } catch (\Throwable $e) {
            return new self([
                'id' => 1,
                'name' => 'SISTEMA POS & COMERCIAL',
                'legal_name' => 'IMDICORP S.R.L.',
                'slogan' => 'Venta y Distribución de Productos y Servicios',
                'nit' => '1023847029',
                'authorization_number' => '4920018471',
                'business_type' => 'GENERAL',
                'plan_name' => 'EMPRESARIAL',
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
        }
    }

    /**
     * Accesor de compatibilidad para plan y vencimiento.
     */
    public function getSubscriptionPlanAttribute()
    {
        return $this->plan_name ?? 'EMPRESARIAL';
    }

    public function getSubscriptionExpiresAtAttribute()
    {
        return $this->subscription_ends_at;
    }

    public function getRubroConfigAttribute()
    {
        return self::rubroProfile($this->business_type ?? 'GENERAL');
    }

    /**
     * Aprovisiona un nuevo Cliente SaaS (Negocio independiente) con su Sucursal, Dueño,
     * Formas de Pago propias y Catálogos iniciales adaptados exactamente a su Rubro.
     */
    public static function provisionNewTenant(array $data)
    {
        return DB::transaction(function () use ($data) {
            $bType = $data['business_type'] ?? 'GENERAL';
            $companyName = $data['name'] ?? ($data['company_name'] ?? 'Nueva Empresa');
            $profile = self::rubroProfile($bType);
            $mods = $profile['modules'];

            $company = self::create([
                'name' => $companyName,
                'legal_name' => $data['legal_name'] ?? $companyName,
                'slogan' => $data['slogan'] ?? $profile['default_slogan'],
                'nit' => $data['nit'] ?? 'S/N',
                'authorization_number' => $data['authorization_number'] ?? null,
                'business_type' => $bType,
                'plan_name' => $data['subscription_plan'] ?? ($data['plan_name'] ?? 'PYME PRO'),
                'subscription_ends_at' => $data['subscription_expires_at'] ?? ($data['subscription_ends_at'] ?? now()->addYear()->toDateString()),
                'max_users' => intval($data['max_users'] ?? 10),
                'max_branches' => intval($data['max_branches'] ?? 3),
                'email' => $data['owner_email'],
                'phone' => $data['phone'] ?? '',
                'address' => $data['address'] ?? '',
                'city' => $data['city'] ?? 'La Paz',
                'country' => $data['country'] ?? 'Bolivia',
                'currency_code' => $data['currency_code'] ?? 'BOB',
                'currency_symbol' => $data['currency_symbol'] ?? 'Bs.',
                'currency_name' => $data['currency_name'] ?? 'Bolivianos',
                'tax_name' => 'IVA',
                'tax_rate' => 13.00,
                'printer_width' => '80mm',
                'receipt_footer' => '¡Gracias por su preferencia en ' . $companyName . '!',
                'terms_conditions' => $profile['default_terms'],
                'default_stock_alert' => 5,
                'enable_pos' => $mods['pos'],
                'enable_credits' => $mods['credits'],
                'enable_quotations' => $mods['quotations'],
                'enable_cash_register' => $mods['cash'],
                'enable_kardex' => $mods['kardex'],
                'enable_lots_expiry' => $mods['lots'],
                'enable_serials_warranties' => $mods['warranties'],
                'enable_services' => $mods['services'],
                'enable_wholesale' => $mods['wholesale'],
                'status' => 'ACTIVO',
            ]);

            // 1. Crear Sucursal Principal del Negocio
            $branch = Branch::withoutGlobalScopes()->create([
                'company_id' => $company->id,
                'code' => 'SUC-01',
                'name' => 'Casa Matriz - ' . $company->name,
                'manager_name' => $data['owner_name'],
                'phone' => $company->phone,
                'email' => $company->email,
                'address' => $company->address,
                'city' => $company->city,
                'is_main' => true,
                'status' => 'ACTIVO',
            ]);

            // 2. Crear Usuario Dueño / Administrador del Negocio
            $adminRole = Role::firstOrCreate(
                ['slug' => 'admin'],
                ['name' => 'Administrador', 'description' => 'Acceso total al negocio', 'special' => 'all-access']
            );

            $owner = User::create([
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'name' => $data['owner_name'],
                'email' => $data['owner_email'],
                'password' => Hash::make($data['owner_password']),
                'status' => 'HABILITADO',
                'is_superadmin' => false,
                'email_verified_at' => now(),
            ]);
            $owner->roles()->syncWithoutDetaching([$adminRole->id]);

            $company->update(['owner_user_id' => $owner->id]);

            // 3. Crear las Formas de Pago propias para este Negocio
            $sym = $company->currency_symbol;
            $defaultPaymentMethods = [
                ['name' => "Efectivo ({$sym})", 'code' => 'EFECTIVO', 'payment_condition' => 'CONTADO', 'description' => 'Cobro en efectivo en caja con cálculo de cambio.', 'requires_reference' => false, 'icon' => 'fas fa-money-bill-wave', 'color' => '#10b981', 'sort_order' => 1],
                ['name' => 'Pago QR Simple', 'code' => 'QR', 'payment_condition' => 'CONTADO', 'description' => 'Cobro mediante código QR bancario del negocio.', 'requires_reference' => true, 'icon' => 'fas fa-qrcode', 'color' => '#6366f1', 'sort_order' => 2],
                ['name' => 'Transferencia Bancaria', 'code' => 'TRANSFERENCIA', 'payment_condition' => 'CONTADO', 'description' => 'Transferencia o depósito a cuenta del negocio.', 'requires_reference' => true, 'icon' => 'fas fa-university', 'color' => '#0284c7', 'sort_order' => 3],
                ['name' => 'Tarjeta Débito / Crédito', 'code' => 'TARJETA', 'payment_condition' => 'AMBOS', 'description' => 'Cobro por terminal POS con tarjeta.', 'requires_reference' => true, 'icon' => 'fas fa-credit-card', 'color' => '#2563eb', 'sort_order' => 4],
                ['name' => 'Crédito Directo', 'code' => 'CREDITO_DIRECTO', 'payment_condition' => 'CREDITO', 'description' => 'Venta a crédito con plan de cuotas.', 'requires_reference' => false, 'icon' => 'fas fa-hand-holding-usd', 'color' => '#f59e0b', 'sort_order' => 5],
            ];

            foreach ($defaultPaymentMethods as $pm) {
                DB::table('payment_methods')->insert(array_merge($pm, [
                    'company_id' => $company->id,
                    'status' => 'ACTIVO',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }

            // 4. Sembrar Categorías y Marcas/Laboratorios específicos según el Rubro elegido
            foreach ($profile['seed_categories'] as $catName => $catDesc) {
                DB::table('categories')->insert([
                    'company_id' => $company->id,
                    'name' => $catName,
                    'description' => $catDesc,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($profile['seed_brands'] as $brandName => $brandDesc) {
                DB::table('brands')->insert([
                    'company_id' => $company->id,
                    'name' => $brandName,
                    'description' => $brandDesc,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('providers')->insert([
                'company_id' => $company->id,
                'name' => 'Proveedor Principal ' . $profile['short_label'],
                'email' => 'compras@' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $company->name ?: 'negocio')) . '.com',
                'nit' => '0',
                'address' => $company->city,
                'phone' => '0000000',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('clients')->insert([
                'company_id' => $company->id,
                'name' => 'SIN NOMBRE (CONSUMIDOR FINAL)',
                'ci' => '0',
                'nit' => '0',
                'address' => $company->city,
                'phone' => '0',
                'email' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $company;
        });
    }

    /**
     * Configuración operativa profunda por cada Tipo de Negocio (Rubro SaaS).
     */
    public static function rubroProfile($type = 'GENERAL')
    {
        $profiles = self::businessTypes();
        return $profiles[$type] ?? $profiles['GENERAL'];
    }

    public static function businessTypes()
    {
        return [
            'GENERAL' => [
                'label' => 'Comercio General / Multi-Rubro',
                'short_label' => 'Comercio General',
                'icon' => 'fas fa-store',
                'desc' => 'Configuración versátil con inventario, POS, crédito, cotizaciones y caja para cualquier tienda.',
                'default_slogan' => 'Comercialización de Productos y Servicios en General',
                'default_terms' => 'Todo cambio o reclamo debe realizarse dentro de las 48 horas presentando este comprobante.',
                'product_title' => 'Productos y Servicios',
                'brand_label' => 'Marca / Fabricante',
                'sku_label' => 'SKU / Código Interno',
                'sku_placeholder' => 'Ej: SKU-0012',
                'batch_label' => 'N° de Lote / Serie',
                'batch_placeholder' => 'Ej: LOTE-2026',
                'location_label' => 'Ubicación en Almacén',
                'location_placeholder' => 'Ej: Estante A-1',
                'pos_notes_label' => 'Observaciones de la Venta',
                'pos_notes_placeholder' => 'Nota opcional para el comprobante...',
                'default_unit' => 'UND',
                'default_item_type' => 'PRODUCTO',
                'modules' => ['pos' => true, 'credits' => true, 'quotations' => true, 'cash' => true, 'kardex' => true, 'lots' => true, 'warranties' => true, 'services' => true, 'wholesale' => true],
                'seed_categories' => [
                    'General' => 'Artículos generales del negocio',
                    'Ofertas y Promociones' => 'Productos destacados o en promoción',
                ],
                'seed_brands' => [
                    'Genérico' => 'Marca estándar o sin marca específica',
                ],
            ],
            'MINIMARKET' => [
                'label' => 'Supermercado / Minimarket / Licorería',
                'short_label' => 'Minimarket',
                'icon' => 'fas fa-shopping-basket',
                'desc' => 'Venta ultrarrápida con lector de código de barras, control de perecederos/vencimientos y precios por paquete.',
                'default_slogan' => 'Abarrotes, Bebidas, Lácteos y Productos de Primera Necesidad',
                'default_terms' => 'Revise sus productos y su cambio antes de retirarse de caja. No se aceptan devoluciones en perecederos.',
                'product_title' => 'Abarrotes, Bebidas y Perecederos',
                'brand_label' => 'Marca Comercial',
                'sku_label' => 'Código Corto / PLU Balanza',
                'sku_placeholder' => 'Ej: PLU-104 o Pack x6',
                'batch_label' => 'Lote de Producción',
                'batch_placeholder' => 'Ej: LOTE-FEB26',
                'location_label' => 'Góndola / Pasillo / Frigorífico',
                'location_placeholder' => 'Ej: Góndola 2 - Nivel Medio / Cooler 1',
                'pos_notes_label' => 'Nota de Caja / Delivery',
                'pos_notes_placeholder' => 'Ej: Envío a domicilio / Bolsa extra...',
                'default_unit' => 'UND',
                'default_item_type' => 'PRODUCTO',
                'modules' => ['pos' => true, 'credits' => true, 'quotations' => false, 'cash' => true, 'kardex' => true, 'lots' => true, 'warranties' => false, 'services' => false, 'wholesale' => true],
                'seed_categories' => [
                    'Abarrotes y Despensa' => 'Arroz, fideos, aceite, azúcar y enlatados',
                    'Lácteos, Fiambres y Embutidos' => 'Leche, yogurt, quesos y refrigerados',
                    'Bebidas, Aguas y Gaseosas' => 'Refrescos, jugos, aguas y bebidas',
                    'Snacks y Confitería' => 'Galletas, chocolates y dulces',
                    'Limpieza y Hogar' => 'Detergentes, jabones y artículos de aseo',
                ],
                'seed_brands' => [
                    'PIL Andina' => 'Productos lácteos y derivados',
                    'Coca-Cola / CBN' => 'Bebidas y gaseosas',
                    'Fino / Kris' => 'Aceites y alimentos',
                    'Genérico' => 'Productos varios',
                ],
            ],
            'FARMACIA' => [
                'label' => 'Farmacia / Botica / Insumos Médicos',
                'short_label' => 'Farmacia',
                'icon' => 'fas fa-capsules',
                'desc' => 'Control estricto de lotes farmacéuticos, semáforo de fechas de vencimiento, principio activo, laboratorio y estante.',
                'default_slogan' => 'Salud, Medicamentos Garantizados y Atención Farmacéutica Profesional',
                'default_terms' => 'Por normas sanitarias, no se aceptan cambios ni devoluciones en medicamentos una vez retirados del mostrador.',
                'product_title' => 'Medicamentos e Insumos Farmacéuticos',
                'brand_label' => 'Laboratorio Farmacéutico',
                'sku_label' => 'Principio Activo / Reg. Sanitario',
                'sku_placeholder' => 'Ej: Paracetamol 500mg / RS-II-39201',
                'batch_label' => 'N° de Lote Sanitario',
                'batch_placeholder' => 'Ej: LOTE-9482A',
                'location_label' => 'Anaquel / Gaveta / Cadena de Frío',
                'location_placeholder' => 'Ej: Gaveta A-12 / Estante Antibióticos',
                'pos_notes_label' => 'Paciente / Médico / Receta',
                'pos_notes_placeholder' => 'Ej: Receta Dr. Pérez / Indicaciones...',
                'default_unit' => 'CAJA',
                'default_item_type' => 'PRODUCTO',
                'modules' => ['pos' => true, 'credits' => false, 'quotations' => true, 'cash' => true, 'kardex' => true, 'lots' => true, 'warranties' => false, 'services' => true, 'wholesale' => true],
                'seed_categories' => [
                    'Analgésicos y Antiinflamatorios' => 'Medicamentos para el dolor y fiebre',
                    'Antibióticos y Antivirales' => 'Medicamentos para infecciones bajo receta',
                    'Antigripales y Respiratorios' => 'Jarabes, comprimidos y descongestionantes',
                    'Vitaminas y Suplementos' => 'Multivitamínicos y nutricionales',
                    'Insumos Médicos y Primeros Auxilios' => 'Jeringas, gasas, alcohol y vendas',
                    'Cuidado Personal y Dermocosmética' => 'Higiene, pediatría y cuidado de la piel',
                ],
                'seed_brands' => [
                    'Droguería INTI' => 'Laboratorio farmacéutico nacional',
                    'Laboratorios Bagó' => 'Especialidades farmacéuticas',
                    'Laboratorios Cofar' => 'Medicamentos certificados',
                    'Bayer / Genérico' => 'Laboratorio internacional / genérico',
                ],
            ],
            'FERRETERIA' => [
                'label' => 'Ferretería / Construcción / Repuestos',
                'short_label' => 'Ferretería',
                'icon' => 'fas fa-tools',
                'desc' => 'Unidades técnicas (Metro, Kg, Bolsa, Rollo, Caja), precios para contratistas/mayoristas, proformas de obra y créditos.',
                'default_slogan' => 'Herramientas, Materiales de Construcción, Electricidad y Plomería',
                'default_terms' => 'Material cortado a medida (cables, mangueras, fierros) o bolsas abiertas de cemento/yeso no tienen cambio.',
                'product_title' => 'Herramientas, Materiales y Repuestos',
                'brand_label' => 'Marca / Fabricante Industrial',
                'sku_label' => 'Medida / Calibre / N° de Parte',
                'sku_placeholder' => 'Ej: 1/2 Pulgada / Calibre 12 AWG',
                'batch_label' => 'Serie / Partida de Importación',
                'batch_placeholder' => 'Ej: IMP-2026-04',
                'location_label' => 'Pasillo / Rack / Galpón',
                'location_placeholder' => 'Ej: Pasillo 2 - Casillero B4 / Patio Cemento',
                'pos_notes_label' => 'Obra / Maestro Contratista / Transporte',
                'pos_notes_placeholder' => 'Ej: Entrega en Obra Zona Norte / Ing. Mamani...',
                'default_unit' => 'UND',
                'default_item_type' => 'PRODUCTO',
                'modules' => ['pos' => true, 'credits' => true, 'quotations' => true, 'cash' => true, 'kardex' => true, 'lots' => false, 'warranties' => true, 'services' => true, 'wholesale' => true],
                'seed_categories' => [
                    'Herramientas Manuales y Eléctricas' => 'Taladros, amoladoras, llaves y martillos',
                    'Material Eléctrico e Iluminación' => 'Cables, térmicos, focos LED y enchufes',
                    'Plomería, Tuberías y Grifería' => 'Tubos PVC, llaves de paso, bombas y accesorios',
                    'Pinturas, Solventes y Acabados' => 'Pinturas látex, sintéticas, brochas y rodillos',
                    'Tornillería, Clavos y Fijaciones' => 'Pernos, tuercas, ramplugs y discos de corte',
                    'Construcción Pesada y Cemento' => 'Cemento, estuco, fierro corrugado y alambre',
                ],
                'seed_brands' => [
                    'Truper / Pretul' => 'Herramientas y ferretería general',
                    'Bosch / Makita / DeWalt' => 'Herramientas eléctricas industriales',
                    'Tigre / Plasmar' => 'Tuberías y accesorios PVC',
                    'Monopol / Sinteplast' => 'Pinturas y recubrimientos',
                ],
            ],
            'ELECTRONICA' => [
                'label' => 'Electrónica / Celulares / Computación',
                'short_label' => 'Electrónica',
                'icon' => 'fas fa-laptop',
                'desc' => 'Control de días de garantía impresos en factura, modelo/IMEI/N° serie, servicio técnico, proformas y ventas a crédito.',
                'default_slogan' => 'Tecnología, Smartphones, Computadoras, Accesorios y Servicio Técnico',
                'default_terms' => 'La garantía cubre exclusivamente defectos de fábrica. No cubre golpes, humedad, pantallas rotas ni sobrecargas eléctricas.',
                'product_title' => 'Equipos, Dispositivos, Accesorios y Servicios',
                'brand_label' => 'Marca Tecnológica',
                'sku_label' => 'Modelo Exacto / N° Serie / IMEI',
                'sku_placeholder' => 'Ej: iPhone 15 128GB / IMEI-358901...',
                'batch_label' => 'N° Serie / Lote Importación',
                'batch_placeholder' => 'Ej: SN-9084712',
                'location_label' => 'Vitrina / Gabinete de Seguridad',
                'location_placeholder' => 'Ej: Vitrina Central 1 - Nivel 2',
                'pos_notes_label' => 'IMEI / Serie Entregada / Condición de Garantía',
                'pos_notes_placeholder' => 'Ej: S/N: 84920184 - Incluye cargador original...',
                'default_unit' => 'UND',
                'default_item_type' => 'PRODUCTO',
                'modules' => ['pos' => true, 'credits' => true, 'quotations' => true, 'cash' => true, 'kardex' => true, 'lots' => false, 'warranties' => true, 'services' => true, 'wholesale' => true],
                'seed_categories' => [
                    'Smartphones y Tablets' => 'Teléfonos móviles y tabletas',
                    'Laptops, PCs y Monitores' => 'Equipos de computación de escritorio y portátiles',
                    'Accesorios, Cargadores y Audio' => 'Fundas, cables, audífonos y parlantes',
                    'Componentes, Redes y Almacenamiento' => 'Discos SSD, memorias RAM, routers e impresoras',
                    'Servicio Técnico y Reparaciones' => 'Cambio de pantalla, mantenimiento y software',
                ],
                'seed_brands' => [
                    'Samsung / Apple / Xiaomi' => 'Dispositivos móviles y electrónica',
                    'HP / Lenovo / Asus / Dell' => 'Computación y portátiles',
                    'Kingston / Logitech / TP-Link' => 'Periféricos, redes y memorias',
                ],
            ],
            'ROPA' => [
                'label' => 'Tienda de Ropa / Calzado / Boutique',
                'short_label' => 'Boutique & Calzado',
                'icon' => 'fas fa-tshirt',
                'desc' => 'Gestión ágil por Talla y Color, temporada/colección, código de barras en etiqueta, descuentos y ventas por mayor/docena.',
                'default_slogan' => 'Moda, Prendas de Vestir, Calzado y Accesorios de Temporada',
                'default_terms' => 'Cambios de talla únicamente dentro de los 7 días con etiquetas originales intactas y sin uso. Prendas en liquidación no tienen cambio.',
                'product_title' => 'Prendas de Vestir, Calzado y Accesorios',
                'brand_label' => 'Marca / Diseñador / Línea',
                'sku_label' => 'Talla y Color',
                'sku_placeholder' => 'Ej: Talla M - Negro / Calzado N° 40',
                'batch_label' => 'Temporada / Colección',
                'batch_placeholder' => 'Ej: Otoño-Invierno 2026',
                'location_label' => 'Perchero / Vitrina / Depósito',
                'location_placeholder' => 'Ej: Perchero Central Damas / Estante Calzado 3',
                'pos_notes_label' => 'Detalle de Talla / Cambio / Reserva',
                'pos_notes_placeholder' => 'Ej: Autorizado cambio de talla hasta 7 días...',
                'default_unit' => 'UND',
                'default_item_type' => 'PRODUCTO',
                'modules' => ['pos' => true, 'credits' => true, 'quotations' => false, 'cash' => true, 'kardex' => true, 'lots' => false, 'warranties' => false, 'services' => false, 'wholesale' => true],
                'seed_categories' => [
                    'Poleras, Camisas y Blusas' => 'Prendas superiores para damas y caballeros',
                    'Pantalones, Jeans y Faldas' => 'Prendas inferiores y mezclilla',
                    'Chamarras, Abrigos y Buzos' => 'Prendas de invierno y deportivas',
                    'Calzado, Tenis y Botines' => 'Calzado formal, casual y deportivo',
                    'Accesorios, Bolsos y Cinturones' => 'Complementos de moda y marroquinería',
                ],
                'seed_brands' => [
                    'Colección Propia / Exclusiva' => 'Confección y diseño propio',
                    'Importado Premium' => 'Prendas y calzado de importación',
                ],
            ],
            'RESTAURANTE' => [
                'label' => 'Restaurante / Cafetería / Comida Rápida',
                'short_label' => 'Restaurante & Café',
                'icon' => 'fas fa-utensils',
                'desc' => 'Comanda rápida en POS (En Mesa, Para Llevar, Delivery), platillos elaborados sin bloqueo de stock y control de bebidas.',
                'default_slogan' => 'Gastronomía, Sabor Casero, Bebidas y Atención de Primera',
                'default_terms' => '¡Gracias por su visita! Verifique su pedido al recibirlo.',
                'product_title' => 'Menú: Platillos, Combos, Bebidas e Insumos',
                'brand_label' => 'Área de Preparación (Cocina / Barra)',
                'sku_label' => 'Tamaño / Porción / Tiempo Prep.',
                'sku_placeholder' => 'Ej: Plato Entero / Combo Familiar',
                'batch_label' => 'Lote de Insumo',
                'batch_placeholder' => 'Ej: Insumo Fresco',
                'location_label' => 'Estación (Cocina / Parrilla / Barra)',
                'location_placeholder' => 'Ej: Cocina Caliente / Barra de Bebidas',
                'pos_notes_label' => 'Mesa / Modalidad (Mesa, Llevar, Delivery) / Notas Cocina',
                'pos_notes_placeholder' => 'Ej: MESA 4 - PARA LLEVAR - Sin picante / Con llajua extra...',
                'default_unit' => 'UND',
                'default_item_type' => 'SERVICIO',
                'modules' => ['pos' => true, 'credits' => false, 'quotations' => true, 'cash' => true, 'kardex' => true, 'lots' => true, 'warranties' => false, 'services' => true, 'wholesale' => false],
                'seed_categories' => [
                    'Platos Fuertes y Especialidades' => 'Menú principal preparado al instante',
                    'Comida Rápida, Burgers y Pizzas' => 'Combos, sándwiches y porciones rápidas',
                    'Bebidas Frías, Gaseosas y Jugos' => 'Refrescos embotellados y jugos naturales',
                    'Cafetería, Postres y Pastelería' => 'Café, infusiones, helados y repostería',
                    'Guarniciones y Extras' => 'Porciones adicionales y salsas',
                ],
                'seed_brands' => [
                    'Cocina Principal' => 'Platillos elaborados en cocina',
                    'Barra y Cafetería' => 'Bebidas, jugos y cafés',
                    'Embotellados / Refrigerador' => 'Gaseosas y aguas con control de stock',
                ],
            ],
            'SERVICIOS' => [
                'label' => 'Taller Mecánico / Servicios Técnicos / Consultoría',
                'short_label' => 'Centro de Servicios',
                'icon' => 'fas fa-concierge-bell',
                'desc' => 'Combinación de Mano de Obra / Servicios e Insumos/Repuestos en la misma venta o proforma, garantías y cuentas por cobrar.',
                'default_slogan' => 'Servicios Técnicos Especializados, Mantenimiento y Repuestos',
                'default_terms' => 'Todo trabajo técnico cuenta con garantía sobre la mano de obra realizada según el plazo detallado en este comprobante.',
                'product_title' => 'Catálogo de Servicios, Mano de Obra y Repuestos',
                'brand_label' => 'Especialidad / Marca de Repuesto',
                'sku_label' => 'Código Servicio / Duración Estimada',
                'sku_placeholder' => 'Ej: SRV-01 / Tiempo est: 1 Hora',
                'batch_label' => 'N° Serie de Repuesto',
                'batch_placeholder' => 'Ej: REP-2026',
                'location_label' => 'Área Técnica / Estante de Repuestos',
                'location_placeholder' => 'Ej: Bahía Técnica 1 / Almacén Repuestos',
                'pos_notes_label' => 'Orden de Trabajo / Placa o Equipo / Diagnóstico',
                'pos_notes_placeholder' => 'Ej: OT #105 - Vehículo Placa 4589TGB / Equipo Laptop HP...',
                'default_unit' => 'SERV',
                'default_item_type' => 'SERVICIO',
                'modules' => ['pos' => true, 'credits' => true, 'quotations' => true, 'cash' => true, 'kardex' => true, 'lots' => false, 'warranties' => true, 'services' => true, 'wholesale' => false],
                'seed_categories' => [
                    'Mano de Obra y Servicios Técnicos' => 'Servicios profesionales y reparaciones',
                    'Mantenimiento Preventivo y Diagnóstico' => 'Inspecciones, limpieza y calibración',
                    'Repuestos e Insumos Utilizados' => 'Piezas, lubricantes y consumibles físicos',
                    'Asesoría, Instalación y Proyectos' => 'Trabajos a medida y contratos',
                ],
                'seed_brands' => [
                    'Servicio Propio Garantizado' => 'Mano de obra del taller o empresa',
                    'Repuesto Original / OEM' => 'Insumos y repuestos certificados',
                ],
            ],
        ];
    }
}
