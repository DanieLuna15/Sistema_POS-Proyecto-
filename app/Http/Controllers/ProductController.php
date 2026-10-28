<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use App\Brand;
use App\Provider;
use App\Branch;
use App\BranchProduct;
use App\InventoryMovement;
use App\ProductComboItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('can:products.create')->only(['create', 'store']);
        $this->middleware('can:products.index')->only(['index']);
        $this->middleware('can:products.edit')->only(['edit', 'update']);
        $this->middleware('can:products.show')->only(['show']);
        $this->middleware('can:change.status.products')->only(['change_status']);
    }

    public function index(Request $request)
    {
        $typeFilter = $request->query('type');
        $query = Product::with(['category', 'brand', 'provider', 'branchStocks.branch', 'comboItems.componentProduct'])
            ->orderBy('created_at', 'DESC');

        if (in_array($typeFilter, ['COMBO', 'SERVICIO', 'PRODUCTO'])) {
            $query->where('item_type', $typeFilter);
        }

        $products = $query->get();
        return view('admin.product.index', compact('products', 'typeFilter'));
    }

    public function create()
    {
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        $providers = Provider::orderBy('name', 'ASC')->get();
        $units = Product::unitsCatalog();
        $availableComponents = Product::where('item_type', '!=', 'COMBO')->where('status', 'ACTIVO')->orderBy('name', 'ASC')->get();

        return view('admin.product.create', compact('categories', 'brands', 'providers', 'units', 'availableComponents'));
    }

    public function store(StoreRequest $request)
    {
        $image_name = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $image_name = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
            $file->move(public_path("/image"), $image_name);
        }

        $initialStock = in_array($request->item_type, ['SERVICIO', 'COMBO']) ? 0 : intval($request->stock ?? 0);
        $branch = Branch::current();

        $data = $request->except(['picture', 'stock', 'combo_component_id', 'combo_component_qty']);
        $data['image'] = $image_name;
        $data['stock'] = $initialStock;
        $data['purchase_price'] = floatval($request->purchase_price ?? 0);
        $data['stock_min'] = intval($request->stock_min ?? 5);
        $data['item_type'] = $request->item_type ?? 'PRODUCTO';
        $data['unit_measure'] = $request->unit_measure ?? 'UND';
        $data['branch_id'] = $branch ? $branch->id : null;

        $product = Product::create($data);

        // Si es un COMBO, guardar su receta / componentes y calcular su costo real financiero
        if ($product->item_type === 'COMBO' && is_array($request->combo_component_id)) {
            $calculatedCost = 0;
            foreach ($request->combo_component_id as $idx => $compId) {
                $compQty = floatval($request->combo_component_qty[$idx] ?? 1);
                if ($compId && $compQty > 0) {
                    $compProd = Product::find($compId);
                    if ($compProd) {
                        ProductComboItem::updateOrCreate(
                            [
                                'combo_product_id' => $product->id,
                                'component_product_id' => $compProd->id,
                            ],
                            [
                                'quantity' => $compQty,
                            ]
                        );
                        $calculatedCost += floatval($compProd->purchase_price) * $compQty;
                    }
                }
            }
            if ($product->purchase_price <= 0 && $calculatedCost > 0) {
                $product->update(['purchase_price' => $calculatedCost]);
            }
        }

        // Si el usuario no ingresó un código de barras manual, autogenerar uno limpio basado en ID
        if (empty($product->code)) {
            $product->update([
                'code' => str_pad($product->id, 6, '0', STR_PAD_LEFT),
            ]);
        }

        // Registrar el stock inicial independiente en la Sucursal Activa
        if ($branch && !$product->isService()) {
            try {
                BranchProduct::updateOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'company_id' => $product->company_id,
                        'stock' => $initialStock,
                        'stock_min' => $product->stock_min,
                        'location' => $product->location,
                    ]
                );
            } catch (\Throwable $e) {}
        }

        // Si se registró con stock inicial > 0, dejar trazabilidad en el Kardex
        if ($initialStock > 0 && !$product->isService()) {
            try {
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'branch_id' => $branch ? $branch->id : null,
                    'user_id' => Auth::id(),
                    'movement_type' => 'INVENTARIO_INICIAL',
                    'quantity' => $initialStock,
                    'previous_stock' => 0,
                    'new_stock' => $initialStock,
                    'unit_cost' => $product->purchase_price ?? 0,
                    'reference_code' => 'INI-' . str_pad($product->id, 5, '0', STR_PAD_LEFT),
                    'notes' => 'Stock inicial al registrar el producto',
                    'movement_date' => Carbon::now('America/La_Paz'),
                ]);
            } catch (\Throwable $e) {}
        }

        Alert::toast('Producto / Servicio registrado con éxito.', 'success');
        return redirect()->route('products.index');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'provider', 'branchStocks.branch', 'movements.user', 'movements.branch', 'comboItems.componentProduct']);
        return view('admin.product.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['comboItems.componentProduct']);
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        $providers = Provider::orderBy('name', 'ASC')->get();
        $units = Product::unitsCatalog();
        $availableComponents = Product::where('item_type', '!=', 'COMBO')
            ->where('id', '!=', $product->id)
            ->where('status', 'ACTIVO')
            ->orderBy('name', 'ASC')
            ->get();

        return view('admin.product.edit', compact('product', 'categories', 'brands', 'providers', 'units', 'availableComponents'));
    }

    public function update(UpdateRequest $request, Product $product)
    {
        $data = $request->except(['picture', 'combo_component_id', 'combo_component_qty']);

        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $image_name = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
            $file->move(public_path("/image"), $image_name);

            if ($product->image && file_exists(public_path('/image/' . $product->image))) {
                @unlink(public_path('/image/' . $product->image));
            }

            $data['image'] = $image_name;
        }

        if (empty($data['code'])) {
            $data['code'] = $product->code ?: str_pad($product->id, 6, '0', STR_PAD_LEFT);
        }

        $product->update($data);

        // Si es un COMBO, sincronizar su receta / componentes
        if ($product->item_type === 'COMBO' && $request->has('combo_component_id')) {
            ProductComboItem::where('combo_product_id', $product->id)->delete();
            $calculatedCost = 0;
            if (is_array($request->combo_component_id)) {
                foreach ($request->combo_component_id as $idx => $compId) {
                    $compQty = floatval($request->combo_component_qty[$idx] ?? 1);
                    if ($compId && $compQty > 0) {
                        $compProd = Product::find($compId);
                        if ($compProd) {
                            ProductComboItem::create([
                                'combo_product_id' => $product->id,
                                'component_product_id' => $compProd->id,
                                'quantity' => $compQty,
                            ]);
                            $calculatedCost += floatval($compProd->purchase_price) * $compQty;
                        }
                    }
                }
            }
            if ($calculatedCost > 0) {
                $product->update(['purchase_price' => $calculatedCost]);
            }
        }

        Alert::toast('Producto actualizado con éxito.', 'success');
        return redirect()->route('products.index');
    }

    public function destroy(Product $product)
    {
    }

    public function change_status(Product $product)
    {
        if ($product->status == 'ACTIVO') {
            $product->update(['status' => 'DESACTIVADO']);
            Alert::toast('Producto Deshabilitado con éxito.', 'success');
            return redirect()->back();
        } else {
            $product->update(['status' => 'ACTIVO']);
            Alert::toast('Producto Habilitado con éxito.', 'success');
            return redirect()->back();
        }
    }
}
