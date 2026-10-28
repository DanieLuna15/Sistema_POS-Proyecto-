<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Company;
use App\Branch;
use App\Category;
use App\Brand;
use App\Provider;
use App\Client;
use App\Product;
use App\BranchProduct;
use App\ProductComboItem;
use App\InventoryMovement;
use App\RestaurantTable;
use App\RestaurantOrder;
use App\RestaurantOrderItem;
use Carbon\Carbon;

class RestaurantDemoSeeder extends Seeder
{
    public function run()
    {
        $company = Company::first() ?: Company::current();
        $company->update([
            'business_type' => 'RESTAURANTE',
            'enable_services' => true,
        ]);

        $branch = Branch::withoutGlobalScopes()->where('company_id', $company->id)->first();
        if (!$branch) {
            $branch = Branch::withoutGlobalScopes()->create([
                'company_id' => $company->id,
                'code' => 'SUC-001',
                'name' => 'Sucursal Central - Salón Gastronómico',
                'address' => 'Av. Principal #100',
                'is_main' => true,
                'status' => 'ACTIVO',
            ]);
        }

        // Asegurar cliente Consumidor Final
        $defaultClient = Client::ensureDefaultForCompany($company->id);

        // 1. Generar ilustraciones SVG gastronómicas en public/image/
        $this->generateFoodSvgImages();

        // 2. Limpiar catálogo anterior de tienda de electrónicos para dejar el restaurante limpio y realista
        Schema::disableForeignKeyConstraints();
        RestaurantOrderItem::truncate();
        RestaurantOrder::truncate();
        RestaurantTable::truncate();
        ProductComboItem::truncate();
        BranchProduct::withoutGlobalScopes()->where('company_id', $company->id)->delete();
        InventoryMovement::withoutGlobalScopes()->where('company_id', $company->id)->delete();
        DB::table('sale_details')->truncate();
        DB::table('sales')->truncate();
        Product::withoutGlobalScopes()->where('company_id', $company->id)->delete();
        Category::withoutGlobalScopes()->where('company_id', $company->id)->delete();
        Brand::withoutGlobalScopes()->where('company_id', $company->id)->delete();
        Schema::enableForeignKeyConstraints();

        // 3. Crear Áreas de Cocina / Marcas Gastronómicas
        $brandsData = [
            'Cocina Caliente & Parrilla' => 'Estación principal de carnes, brasas y platos fuertes',
            'Freidora & Broaster'        => 'Estación de pollos crujientes, papas y hamburguesas',
            'Horno & Pizzería'           => 'Estación de pizzas artesanales y pastas',
            'Barra & Bebidas Frías'      => 'Heladera de gaseosas, cervezas y jugos naturales',
            'Pastelería & Cafetería'     => 'Postres caseros, helados y café expreso',
        ];
        $brands = [];
        foreach ($brandsData as $bName => $bDesc) {
            $brands[$bName] = Brand::withoutGlobalScopes()->create([
                'company_id' => $company->id,
                'name' => $bName,
                'description' => $bDesc,
            ]);
        }

        // 4. Proveedor Gastronómico Principal
        $provider = Provider::withoutGlobalScopes()->where('company_id', $company->id)->first();
        if (!$provider) {
            $provider = Provider::withoutGlobalScopes()->create([
                'company_id' => $company->id,
                'name' => 'Distribuidora Gastronómica & Embotelladora Nacional',
                'email' => 'pedidos@distribuidoragastro.bo',
                'nit_number' => '1020304050',
                'address' => 'Parque Industrial Mz. 4',
                'phone' => '70012345',
            ]);
        }

        // 5. Crear Categorías Gastronómicas
        $categoriesData = [
            'Combos y Promociones'  => 'Combos familiares y personales con descuento promocional y control de stock de bebidas',
            'Carnes y Parrilla'     => 'Cortes selectos a la parrilla, churrascos y platos típicos',
            'Pollos y Broaster'     => 'Pollo a la brasa, broaster crujiente y alitas BBQ',
            'Hamburguesas y Pizzas' => 'Hamburguesas gourmet y pizzas familiares al horno',
            'Bebidas y Gaseosas'    => 'Gaseosas embotelladas, cervezas y jarras naturales con control de stock en Kardex',
            'Postres y Cafetería'   => 'Postres artesanales, helados y cafetería',
        ];
        $categories = [];
        foreach ($categoriesData as $cName => $cDesc) {
            $categories[$cName] = Category::withoutGlobalScopes()->create([
                'company_id' => $company->id,
                'name' => $cName,
                'description' => $cDesc,
            ]);
        }

        // 6. Registrar Platos de Cocina (SERVICIO) y Bebidas con Stock (PRODUCTO)
        $itemsCatalog = [
            // CARNES Y PARRILLA (SERVICIO = Plato elaborado en cocina)
            [
                'key' => 'pique_macho',
                'code' => 'PLT-001',
                'sku' => 'COCINA-PARRILLA',
                'name' => 'Pique Macho Tradicional Grande',
                'item_type' => 'SERVICIO',
                'unit_measure' => 'PLATO',
                'category' => 'Carnes y Parrilla',
                'brand' => 'Cocina Caliente & Parrilla',
                'purchase_price' => 38.00,
                'sell_price' => 75.00,
                'stock' => 0,
                'location' => 'Estación Parrilla 1',
                'image' => 'rest_pique.svg',
                'description' => 'Lomo de res en trozos, chorizo parrillero, papas fritas, huevo, tomate, locoto y salsas.',
            ],
            [
                'key' => 'churrasco',
                'code' => 'PLT-002',
                'sku' => 'COCINA-PARRILLA',
                'name' => 'Churrasco Especial a la Parrilla',
                'item_type' => 'SERVICIO',
                'unit_measure' => 'PLATO',
                'category' => 'Carnes y Parrilla',
                'brand' => 'Cocina Caliente & Parrilla',
                'purchase_price' => 28.00,
                'sell_price' => 55.00,
                'stock' => 0,
                'location' => 'Estación Parrilla 1',
                'image' => 'rest_churrasco.svg',
                'description' => 'Bife jugoso a la brasa acompañado de arroz con queso, yuca frita y ensalada fresca.',
            ],
            [
                'key' => 'bife_chorizo',
                'code' => 'PLT-003',
                'sku' => 'COCINA-PARRILLA',
                'name' => 'Bife Chorizo 400g con Guarnición',
                'item_type' => 'SERVICIO',
                'unit_measure' => 'PLATO',
                'category' => 'Carnes y Parrilla',
                'brand' => 'Cocina Caliente & Parrilla',
                'purchase_price' => 35.00,
                'sell_price' => 68.00,
                'stock' => 0,
                'location' => 'Estación Parrilla 1',
                'image' => 'rest_bife.svg',
                'description' => 'Corte grueso de bife chorizo término a elección con papas rústicas y chimichurri.',
            ],
            [
                'key' => 'milanesa_napo',
                'code' => 'PLT-004',
                'sku' => 'COCINA-CALIENTE',
                'name' => 'Milanesa Napolitana con Papas',
                'item_type' => 'SERVICIO',
                'unit_measure' => 'PLATO',
                'category' => 'Carnes y Parrilla',
                'brand' => 'Cocina Caliente & Parrilla',
                'purchase_price' => 20.00,
                'sell_price' => 42.00,
                'stock' => 0,
                'location' => 'Cocina Caliente',
                'image' => 'rest_milanesa.svg',
                'description' => 'Milanesa gratinada con jamón, queso mozzarella, salsa pomodoro, arroz y papas fritas.',
            ],

            // POLLOS Y BROASTER
            [
                'key' => 'medio_broaster',
                'code' => 'PLT-005',
                'sku' => 'COCINA-BROASTER',
                'name' => 'Medio Pollo Broaster Crujiente',
                'item_type' => 'SERVICIO',
                'unit_measure' => 'PLATO',
                'category' => 'Pollos y Broaster',
                'brand' => 'Freidora & Broaster',
                'purchase_price' => 22.00,
                'sell_price' => 46.00,
                'stock' => 0,
                'location' => 'Estación Broaster',
                'image' => 'rest_broaster.svg',
                'description' => '4 presas de pollo broaster dorado con porción generosa de papas fritas, plátano y llajua.',
            ],
            [
                'key' => 'cuarto_brasa',
                'code' => 'PLT-006',
                'sku' => 'COCINA-BRASA',
                'name' => 'Cuarto Pollo a la Brasa Económico',
                'item_type' => 'SERVICIO',
                'unit_measure' => 'PLATO',
                'category' => 'Pollos y Broaster',
                'brand' => 'Freidora & Broaster',
                'purchase_price' => 12.00,
                'sell_price' => 26.00,
                'stock' => 0,
                'location' => 'Horno Spiedo',
                'image' => 'rest_pollo_brasa.svg',
                'description' => '1/4 de pollo a la leña con papas fritas, arroz y buffet de ensaladas.',
            ],
            [
                'key' => 'alitas_bbq',
                'code' => 'PLT-007',
                'sku' => 'COCINA-BROASTER',
                'name' => 'Alitas BBQ & Miel (10 Piezas)',
                'item_type' => 'SERVICIO',
                'unit_measure' => 'PORCION',
                'category' => 'Pollos y Broaster',
                'brand' => 'Freidora & Broaster',
                'purchase_price' => 18.00,
                'sell_price' => 38.00,
                'stock' => 0,
                'location' => 'Estación Broaster',
                'image' => 'rest_alitas.svg',
                'description' => '10 alitas bañadas en salsa barbacoa ahumada con bastones de apio y salsa ranch.',
            ],

            // HAMBURGUESAS Y PIZZAS
            [
                'key' => 'burger_doble',
                'code' => 'PLT-008',
                'sku' => 'COCINA-PLANCHA',
                'name' => 'Hamburguesa Doble Queso & Tocino',
                'item_type' => 'SERVICIO',
                'unit_measure' => 'PLATO',
                'category' => 'Hamburguesas y Pizzas',
                'brand' => 'Freidora & Broaster',
                'purchase_price' => 15.00,
                'sell_price' => 32.00,
                'stock' => 0,
                'location' => 'Plancha Rápida',
                'image' => 'rest_burger.svg',
                'description' => 'Doble medallón de res a la parrilla, queso cheddar fundido, tocino crocante y papas fritas.',
            ],
            [
                'key' => 'pizza_napo',
                'code' => 'PLT-009',
                'sku' => 'COCINA-HORNO',
                'name' => 'Pizza Napolitana Familiar (8 Porciones)',
                'item_type' => 'SERVICIO',
                'unit_measure' => 'UND',
                'category' => 'Hamburguesas y Pizzas',
                'brand' => 'Horno & Pizzería',
                'purchase_price' => 24.00,
                'sell_price' => 55.00,
                'stock' => 0,
                'location' => 'Horno Piedra',
                'image' => 'rest_pizza.svg',
                'description' => 'Masa artesanal a la piedra con mozzarella doble, rodajas de tomate, ajo y orégano.',
            ],

            // BEBIDAS Y GASEOSAS (PRODUCTO = Controla Stock en Sucursal y Kardex)
            [
                'key' => 'cocacola_2l',
                'code' => 'BEB-001',
                'sku' => 'BARRA-FRIA',
                'name' => 'Coca-Cola Familiar 2 Litros',
                'item_type' => 'PRODUCTO',
                'unit_measure' => 'BOT',
                'category' => 'Bebidas y Gaseosas',
                'brand' => 'Barra & Bebidas Frías',
                'purchase_price' => 9.00,
                'sell_price' => 16.00,
                'stock' => 48,
                'location' => 'Visicooler Barra #1',
                'image' => 'rest_cocacola2l.svg',
                'description' => 'Botella retornable/descartable de 2 Litros bien fría (Controla Stock en Kardex).',
            ],
            [
                'key' => 'cocacola_500',
                'code' => 'BEB-002',
                'sku' => 'BARRA-FRIA',
                'name' => 'Coca-Cola Personal 500ml',
                'item_type' => 'PRODUCTO',
                'unit_measure' => 'BOT',
                'category' => 'Bebidas y Gaseosas',
                'brand' => 'Barra & Bebidas Frías',
                'purchase_price' => 4.50,
                'sell_price' => 8.00,
                'stock' => 60,
                'location' => 'Visicooler Barra #1',
                'image' => 'rest_cocacola500.svg',
                'description' => 'Botella personal de 500ml helada (Controla Stock en Kardex).',
            ],
            [
                'key' => 'cerveza_huari',
                'code' => 'BEB-003',
                'sku' => 'BARRA-CERVEZAS',
                'name' => 'Cerveza Huari Tradicional 620ml',
                'item_type' => 'PRODUCTO',
                'unit_measure' => 'BOT',
                'category' => 'Bebidas y Gaseosas',
                'brand' => 'Barra & Bebidas Frías',
                'purchase_price' => 12.00,
                'sell_price' => 22.00,
                'stock' => 72,
                'location' => 'Cámara Fría Barra',
                'image' => 'rest_cerveza.svg',
                'description' => 'Cerveza premium boliviana botella 620ml bien helada.',
            ],
            [
                'key' => 'jarra_limonada',
                'code' => 'BEB-004',
                'sku' => 'BARRA-JUGOS',
                'name' => 'Jarra de Limonada Frappé 1 Litro',
                'item_type' => 'SERVICIO',
                'unit_measure' => 'L',
                'category' => 'Bebidas y Gaseosas',
                'brand' => 'Barra & Bebidas Frías',
                'purchase_price' => 7.00,
                'sell_price' => 18.00,
                'stock' => 0,
                'location' => 'Barra de Jugos',
                'image' => 'rest_limonada.svg',
                'description' => 'Limonada natural recién licuada con hierbabuena y hielo frappé.',
            ],

            // POSTRES Y CAFETERÍA
            [
                'key' => 'postre_brownie',
                'code' => 'POS-001',
                'sku' => 'CAFETERIA',
                'name' => 'Brownie Tibio con Helado de Vainilla',
                'item_type' => 'SERVICIO',
                'unit_measure' => 'PORCION',
                'category' => 'Postres y Cafetería',
                'brand' => 'Pastelería & Cafetería',
                'purchase_price' => 9.00,
                'sell_price' => 22.00,
                'stock' => 0,
                'location' => 'Vitrina Postres',
                'image' => 'rest_postre.svg',
                'description' => 'Brownie de chocolate belga con bocha de helado artesanal y fudge.',
            ],
        ];

        $createdProducts = [];
        foreach ($itemsCatalog as $row) {
            $prod = Product::withoutGlobalScopes()->create([
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'code' => $row['code'],
                'sku' => $row['sku'],
                'name' => $row['name'],
                'item_type' => $row['item_type'],
                'unit_measure' => $row['unit_measure'],
                'stock' => $row['stock'],
                'stock_min' => 6,
                'purchase_price' => $row['purchase_price'],
                'sell_price' => $row['sell_price'],
                'wholesale_price' => $row['sell_price'],
                'location' => $row['location'],
                'description' => $row['description'],
                'image' => $row['image'],
                'status' => 'ACTIVO',
                'category_id' => $categories[$row['category']]->id,
                'brand_id' => $brands[$row['brand']]->id,
                'provider_id' => $provider->id,
            ]);

            if ($row['item_type'] === 'PRODUCTO' && $row['stock'] > 0) {
                BranchProduct::withoutGlobalScopes()->create([
                    'company_id' => $company->id,
                    'branch_id' => $branch->id,
                    'product_id' => $prod->id,
                    'stock' => $row['stock'],
                    'stock_min' => 6,
                    'location' => $row['location'],
                ]);

                InventoryMovement::withoutGlobalScopes()->create([
                    'company_id' => $company->id,
                    'branch_id' => $branch->id,
                    'product_id' => $prod->id,
                    'user_id' => 1,
                    'movement_type' => 'INVENTARIO_INICIAL',
                    'quantity' => $row['stock'],
                    'previous_stock' => 0,
                    'new_stock' => $row['stock'],
                    'unit_cost' => $row['purchase_price'],
                    'reference_code' => 'SEED-' . $row['code'],
                    'notes' => 'Stock inicial de bebidas en cámara fría de restaurante',
                    'movement_date' => Carbon::now('America/La_Paz'),
                ]);
            }

            $createdProducts[$row['key']] = $prod;
        }

        // 7. CREAR COMBOS PROMOCIONALES (item_type = COMBO) CON RECETA Y DESCUENTO DE STOCK
        $combosDefinition = [
            [
                'code' => 'CMB-001',
                'name' => 'Combo Familiar Broaster + Coca-Cola 2L',
                'sell_price' => 95.00, // Precio normal sería 46*2 + 16 = 108 Bs.
                'image' => 'rest_combo_broaster.svg',
                'description' => 'Incluye: 2x Medio Pollo Broaster Crujiente (8 presas + papas) + 1x Coca-Cola 2 Litros (Descuenta 1 Coca-Cola 2L del Kardex).',
                'components' => [
                    ['key' => 'medio_broaster', 'qty' => 2],
                    ['key' => 'cocacola_2l',    'qty' => 1],
                ],
            ],
            [
                'code' => 'CMB-002',
                'name' => 'Combo Parrillero Pique Macho + 2 Huari',
                'sell_price' => 105.00, // Precio normal sería 75 + 44 = 119 Bs.
                'image' => 'rest_combo_parrilla.svg',
                'description' => 'Incluye: 1x Pique Macho Tradicional Grande + 2x Cerveza Huari 620ml (Descuenta 2 Cervezas Huari del Kardex).',
                'components' => [
                    ['key' => 'pique_macho',   'qty' => 1],
                    ['key' => 'cerveza_huari', 'qty' => 2],
                ],
            ],
            [
                'code' => 'CMB-003',
                'name' => 'Combo Dúo Burgers + 2 Coca-Cola 500ml',
                'sell_price' => 68.00, // Precio normal sería 64 + 16 = 80 Bs.
                'image' => 'rest_combo_burger.svg',
                'description' => 'Incluye: 2x Hamburguesa Doble Queso & Tocino + 2x Coca-Cola Personal 500ml (Descuenta 2 Coca-Colas 500ml del Kardex).',
                'components' => [
                    ['key' => 'burger_doble', 'qty' => 2],
                    ['key' => 'cocacola_500', 'qty' => 2],
                ],
            ],
            [
                'code' => 'CMB-004',
                'name' => 'Combo Almuerzo Churrasco + Jarra Limonada',
                'sell_price' => 64.00, // Precio normal sería 55 + 18 = 73 Bs.
                'image' => 'rest_combo_almuerzo.svg',
                'description' => 'Incluye: 1x Churrasco Especial a la Parrilla + 1x Jarra de Limonada Frappé 1 Litro.',
                'components' => [
                    ['key' => 'churrasco',      'qty' => 1],
                    ['key' => 'jarra_limonada', 'qty' => 1],
                ],
            ],
        ];

        foreach ($combosDefinition as $cmb) {
            // Calcular el costo financiero exacto sumando el costo de cada componente
            $totalCost = 0;
            foreach ($cmb['components'] as $c) {
                $compProd = $createdProducts[$c['key']];
                $totalCost += floatval($compProd->purchase_price) * floatval($c['qty']);
            }

            $comboProduct = Product::withoutGlobalScopes()->create([
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'code' => $cmb['code'],
                'sku' => 'COMBO-PROMO',
                'name' => $cmb['name'],
                'item_type' => 'COMBO',
                'unit_measure' => 'COMBO',
                'stock' => 0,
                'stock_min' => 0,
                'purchase_price' => $totalCost,
                'sell_price' => $cmb['sell_price'],
                'wholesale_price' => $cmb['sell_price'],
                'location' => 'Cocina + Barra',
                'description' => $cmb['description'],
                'image' => $cmb['image'],
                'status' => 'ACTIVO',
                'category_id' => $categories['Combos y Promociones']->id,
                'brand_id' => $brands['Cocina Caliente & Parrilla']->id,
                'provider_id' => $provider->id,
            ]);

            foreach ($cmb['components'] as $c) {
                $compProd = $createdProducts[$c['key']];
                ProductComboItem::create([
                    'combo_product_id' => $comboProduct->id,
                    'component_product_id' => $compProd->id,
                    'quantity' => $c['qty'],
                ]);
            }

            $createdProducts[$cmb['code']] = $comboProduct;
        }

        // 8. CREAR 4 ÁREAS / AMBIENTES CON 14 MESAS ORGANIZADAS EN EL PLANO 2D
        $tablesLayout = [
            // ÁREA 1: Salón Principal (6 Mesas bien distribuidas)
            ['name' => 'Mesa 1', 'zone' => 'Salón Principal', 'capacity' => 4, 'shape' => 'CUADRADA',    'pos_x' => 40,  'pos_y' => 60],
            ['name' => 'Mesa 2', 'zone' => 'Salón Principal', 'capacity' => 4, 'shape' => 'CUADRADA',    'pos_x' => 230, 'pos_y' => 60],
            ['name' => 'Mesa 3', 'zone' => 'Salón Principal', 'capacity' => 6, 'shape' => 'RECTANGULAR', 'pos_x' => 420, 'pos_y' => 60],
            ['name' => 'Mesa 4', 'zone' => 'Salón Principal', 'capacity' => 4, 'shape' => 'CUADRADA',    'pos_x' => 40,  'pos_y' => 210],
            ['name' => 'Mesa 5', 'zone' => 'Salón Principal', 'capacity' => 4, 'shape' => 'REDONDA',     'pos_x' => 230, 'pos_y' => 210],
            ['name' => 'Mesa 6', 'zone' => 'Salón Principal', 'capacity' => 8, 'shape' => 'RECTANGULAR', 'pos_x' => 420, 'pos_y' => 210],

            // ÁREA 2: Terraza / Jardín (3 Mesas al aire libre)
            ['name' => 'Terraza 1', 'zone' => 'Terraza / Jardín', 'capacity' => 4, 'shape' => 'REDONDA',     'pos_x' => 50,  'pos_y' => 60],
            ['name' => 'Terraza 2', 'zone' => 'Terraza / Jardín', 'capacity' => 4, 'shape' => 'REDONDA',     'pos_x' => 240, 'pos_y' => 60],
            ['name' => 'Terraza 3', 'zone' => 'Terraza / Jardín', 'capacity' => 6, 'shape' => 'RECTANGULAR', 'pos_x' => 430, 'pos_y' => 60],

            // ÁREA 3: Salón VIP (2 Mesas ejecutivas familiares)
            ['name' => 'VIP 1', 'zone' => 'Salón VIP', 'capacity' => 8,  'shape' => 'RECTANGULAR', 'pos_x' => 50,  'pos_y' => 60],
            ['name' => 'VIP 2', 'zone' => 'Salón VIP', 'capacity' => 10, 'shape' => 'RECTANGULAR', 'pos_x' => 290, 'pos_y' => 60],

            // ÁREA 4: Barra & Café (3 Taburetes / Mesas rápidas)
            ['name' => 'Barra 1', 'zone' => 'Barra & Café', 'capacity' => 2, 'shape' => 'BARRA', 'pos_x' => 50,  'pos_y' => 60],
            ['name' => 'Barra 2', 'zone' => 'Barra & Café', 'capacity' => 2, 'shape' => 'BARRA', 'pos_x' => 220, 'pos_y' => 60],
            ['name' => 'Barra 3', 'zone' => 'Barra & Café', 'capacity' => 2, 'shape' => 'BARRA', 'pos_x' => 390, 'pos_y' => 60],
        ];

        $createdTables = [];
        foreach ($tablesLayout as $tData) {
            $createdTables[$tData['name']] = RestaurantTable::withoutGlobalScopes()->create([
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'name' => $tData['name'],
                'zone' => $tData['zone'],
                'capacity' => $tData['capacity'],
                'shape' => $tData['shape'],
                'pos_x' => $tData['pos_x'],
                'pos_y' => $tData['pos_y'],
                'status' => 'LIBRE',
            ]);
        }

        // 9. CREAR 2 COMANDAS ACTIVAS DE DEMOSTRACIÓN PARA QUE EL MONITOR DE COCINA Y EL MAPA LUZCAN VIVOS
        // Comanda 1 en Mesa 1 (Salón Principal)
        $mesa1 = $createdTables['Mesa 1'];
        $order1 = RestaurantOrder::withoutGlobalScopes()->create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'restaurant_table_id' => $mesa1->id,
            'user_id' => 1,
            'client_id' => optional($defaultClient)->id,
            'order_number' => 'CMD-00001',
            'service_type' => 'MESA',
            'diners_count' => 4,
            'status' => 'EN_COCINA',
            'opened_at' => Carbon::now('America/La_Paz')->subMinutes(8),
        ]);
        RestaurantOrderItem::create([
            'restaurant_order_id' => $order1->id,
            'product_id' => $createdProducts['CMB-001']->id,
            'quantity' => 1,
            'price' => $createdProducts['CMB-001']->sell_price,
            'discount' => 0,
            'kitchen_status' => 'EN_COCINA',
            'notes' => 'Papas bien doradas, Coca-Cola sin hielo',
        ]);
        RestaurantOrderItem::create([
            'restaurant_order_id' => $order1->id,
            'product_id' => $createdProducts['churrasco']->id,
            'quantity' => 1,
            'price' => $createdProducts['churrasco']->sell_price,
            'discount' => 0,
            'kitchen_status' => 'LISTO',
            'notes' => 'Término tres cuartos',
        ]);
        $order1->recalculate();
        $mesa1->update(['status' => 'OCUPADA', 'active_order_id' => $order1->id]);

        // Comanda 2 en Terraza 1 (Terraza / Jardín)
        $terraza1 = $createdTables['Terraza 1'];
        $order2 = RestaurantOrder::withoutGlobalScopes()->create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'restaurant_table_id' => $terraza1->id,
            'user_id' => 1,
            'client_id' => optional($defaultClient)->id,
            'order_number' => 'CMD-00002',
            'service_type' => 'MESA',
            'diners_count' => 3,
            'status' => 'EN_COCINA',
            'opened_at' => Carbon::now('America/La_Paz')->subMinutes(4),
        ]);
        RestaurantOrderItem::create([
            'restaurant_order_id' => $order2->id,
            'product_id' => $createdProducts['pique_macho']->id,
            'quantity' => 1,
            'price' => $createdProducts['pique_macho']->sell_price,
            'discount' => 0,
            'kitchen_status' => 'EN_COCINA',
            'notes' => 'Con bastante locoto aparte',
        ]);
        RestaurantOrderItem::create([
            'restaurant_order_id' => $order2->id,
            'product_id' => $createdProducts['jarra_limonada']->id,
            'quantity' => 1,
            'price' => $createdProducts['jarra_limonada']->sell_price,
            'discount' => 0,
            'kitchen_status' => 'SERVIDO',
            'notes' => 'Poco azúcar',
        ]);
        $order2->recalculate();
        $terraza1->update(['status' => 'OCUPADA', 'active_order_id' => $order2->id]);
    }

    /**
     * Genera ilustraciones SVG gastronómicas atractivas en public/image/
     */
    protected function generateFoodSvgImages()
    {
        $dir = public_path('image');
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $cards = [
            'rest_pique.svg'          => ['#7c2d12', '#ea580c', '🥩🍟', 'PIQUE MACHO', 'Lomo, Chorizo y Papas'],
            'rest_churrasco.svg'      => ['#991b1b', '#ef4444', '🥩🔥', 'CHURRASCO', 'Corte a la Parrilla'],
            'rest_bife.svg'           => ['#881337', '#e11d48', '🥩🍷', 'BIFE CHORIZO', '400g A las Brasas'],
            'rest_milanesa.svg'       => ['#9a3412', '#f97316', '🧀🍖', 'NAPOLITANA', 'Gratinada con Papas'],
            'rest_broaster.svg'       => ['#b45309', '#f59e0b', '🍗🍟', 'POLLO BROASTER', 'Crujiente + Papas'],
            'rest_pollo_brasa.svg'    => ['#92400e', '#d97706', '🍗🔥', 'POLLO BRASA', 'Cuarto a la Leña'],
            'rest_alitas.svg'         => ['#7f1d1d', '#dc2626', '🍗🌶️', 'ALITAS BBQ', '10 Piezas Ahumadas'],
            'rest_burger.svg'         => ['#78350f', '#eab308', '🍔🧀', 'DOBLE BURGER', 'Cheddar & Tocino'],
            'rest_pizza.svg'          => ['#991b1b', '#f59e0b', '🍕🍅', 'PIZZA FAMILIAR', 'Mozzarella a la Piedra'],
            'rest_cocacola2l.svg'     => ['#991b1b', '#dc2626', '🥤❄️', 'COCA-COLA 2L', 'Familiar Helada'],
            'rest_cocacola500.svg'    => ['#7f1d1d', '#ef4444', '🥤🧊', 'COCA-COLA 500ml', 'Personal Helada'],
            'rest_cerveza.svg'        => ['#713f12', '#ca8a04', '🍺🌾', 'CERVEZA HUARI', 'Botella 620ml'],
            'rest_limonada.svg'       => ['#14532d', '#22c55e', '🍋🧊', 'JARRA LIMONADA', '1 Litro Frappé'],
            'rest_postre.svg'         => ['#4c1d95', '#8b5cf6', '🍫🍨', 'BROWNIE HELADO', 'Postre Artesanal'],
            'rest_combo_broaster.svg' => ['#1e1b4b', '#4f46e5', '🎁🍗🥤', 'COMBO FAMILIAR', '2 Broaster + Coca 2L'],
            'rest_combo_parrilla.svg' => ['#31102f', '#be185d', '🎁🥩🍺', 'COMBO PARRILLERO', 'Pique + 2 Huari'],
            'rest_combo_burger.svg'   => ['#1e3a8a', '#2563eb', '🎁🍔🥤', 'COMBO DÚO BURGER', '2 Burgers + 2 Cocas'],
            'rest_combo_almuerzo.svg' => ['#064e3b', '#059669', '🎁🥩🍋', 'COMBO ALMUERZO', 'Churrasco + Limonada'],
        ];

        foreach ($cards as $filename => $cfg) {
            [$bg1, $bg2, $emoji, $title, $subtitle] = $cfg;
            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="320" height="220" viewBox="0 0 320 220">
  <defs>
    <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$bg1}"/>
      <stop offset="100%" stop-color="{$bg2}"/>
    </linearGradient>
  </defs>
  <rect width="320" height="220" rx="22" fill="url(#g)"/>
  <circle cx="160" cy="82" r="52" fill="rgba(255,255,255,0.16)"/>
  <text x="160" y="98" font-family="Segoe UI Emoji, Apple Color Emoji, sans-serif" font-size="44" text-anchor="middle">{$emoji}</text>
  <text x="160" y="162" font-family="Segoe UI, Arial, sans-serif" font-size="20" font-weight="800" fill="#ffffff" text-anchor="middle" letter-spacing="0.5">{$title}</text>
  <text x="160" y="188" font-family="Segoe UI, Arial, sans-serif" font-size="13" font-weight="600" fill="rgba(255,255,255,0.88)" text-anchor="middle">{$subtitle}</text>
</svg>
SVG;
            file_put_contents($dir . DIRECTORY_SEPARATOR . $filename, $svg);
        }
    }
}

if (!class_exists('RestaurantDemoSeeder')) {
    class_alias(RestaurantDemoSeeder::class, 'RestaurantDemoSeeder');
}
