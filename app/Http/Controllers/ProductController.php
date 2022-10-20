<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use App\Brand;
use App\Provider;
use Illuminate\Http\Request;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;

use Illuminate\Support\Facades\Auth;
//Para sweet alert en Productos
use RealRashid\SweetAlert\Facades\Alert;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $products = Product::get();
        return view('admin.product.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::get();
        $brands = Brand::get();
        $providers = Provider::get();
        return view('admin.product.create', compact('categories','brands','providers'));
    }

    public function store(StoreRequest $request)
    {
        if($request->hasFile('picture')){
            $file = $request->file('picture');
            $image_name = time().'_'.$file->getClientOriginalName();
            $file->move(public_path("/image"),$image_name);
        }
        $product = Product::create($request->all()+[
            'image'=>$image_name,
        ]);

        $product->update(['code'=>$product->id]);
        Alert::toast('Producto registrado con éxito.', 'success');
        return redirect()->route('products.index');
    }

    public function show(Product $product)
    {
        return view('admin.product.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::get();
        $brands = Brand::get();
        $providers = Provider::get();
        return view('admin.product.edit', compact('product','categories','brands','providers'));
    }

    public function update(UpdateRequest $request, Product $product)
    {
        if($request->hasFile('picture')){
            $file = $request->file('picture');
            $image_name = time().'_'.$file->getClientOriginalName();
            $file->move(public_path("/image"),$image_name);
        }
        $product -> update($request->all()+[
            'image'=>$image_name,
        ]);
        return redirect()->route('products.index');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index');
    }
    public function change_status(Product $product)
    {
        if($product->status == 'ACTIVO'){
            $product->update([ 'status' =>'DESACTIVADO']);
            return redirect()->back();
        }else{
            $product->update([ 'status' =>'ACTIVO']);
            return redirect()->back();
        }
    }
}
