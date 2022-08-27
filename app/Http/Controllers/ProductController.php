<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use App\Brand;
use App\Provider;
use Illuminate\Http\Request;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;

class ProductController extends Controller
{
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
        Product::create($request->all());
        return redirect()->route(products.index);
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
        $product->update($request->all());
        return redirect()->route(products.index);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route(products.index);
    }
}
