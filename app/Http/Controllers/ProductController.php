<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use App\Brand;
use App\Provider;
use App\Branch;
use App\BranchProduct;
use App\InventoryMovement;
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

    public function index()
    {
        $products = Product::with(['category', 'brand', 'provider', 'branchStocks.branch'])->orderBy('created_at', 'DESC')->get();
        return view('admin.product.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        $providers = Provider::orderBy('name', 'ASC')->get();
        $units = Product::unitsCatalog();

        return view('admin.product.create', compact('categories', 'brands', 'providers', 'units'));
    }

    public function store(StoreRequest $request)
    {
        $image_name = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $image_name = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
            $file->move(public_path("/image"), $image_name);
        }

        $initialStock = ($request->item_type === 'SERVICIO') ? 0 : intval($request->stock ?? 0);
        $branch = Branch::current();

        $data = $request->except(['picture', 'stock']);
        $data['image'] = $image_name;
        $data['stock'] = $initialStock;
        $data['purchase_price'] = floatval($request->purchase_price ?? 0);
        $data['stock_min'] = intval($request->stock_min ?? 5);
        $data['item_type'] = $request->item_type ?? 'PRODUCTO';
        $data['unit_measure'] = $request->unit_measure ?? 'UND';
        $data['branch_id'] = $branch ? $branch->id : null;

        $product = Product::create($data);

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
        $product->load(['category', 'brand', 'provider', 'branchStocks.branch', 'movements.user', 'movements.branch']);
        return view('admin.product.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        $providers = Provider::orderBy('name', 'ASC')->get();
        $units = Product::unitsCatalog();

        return view('admin.product.edit', compact('product', 'categories', 'brands', 'providers', 'units'));
    }

    public function update(UpdateRequest $request, Product $product)
    {
        $data = $request->except(['picture']);

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
