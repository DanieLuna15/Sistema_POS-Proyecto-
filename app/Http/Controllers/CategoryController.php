<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Requests\Category\StoreRequest;
use App\Http\Requests\Category\UpdateRequest;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
//Para sweet alert en Categorias
use RealRashid\SweetAlert\Facades\Alert;
class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('can:categories.create')->only(['create','store']);
        $this->middleware('can:categories.index')->only(['index']);
        $this->middleware('can:categories.edit')->only(['edit','update']);
        $this->middleware('can:categories.show')->only(['show']);
    }

    public function index()
    {
        $cantprodCategory=DB::select('SELECT c.name , count(p.id) as cantProduct
        FROM categories as c inner join products as p
        on c.id = p.category_id
        WHERE p.status="ACTIVO"
        group by c.name');

        $categories = Category::get();
        return view('admin.category.index', compact('categories','cantprodCategory'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(StoreRequest $request)
    {
        Category::create($request->all());
        Alert::toast('Categoría registrada con éxito.', 'success');
        return redirect()->route('categories.index');
    }

    public function show(Category $category)
    {
        return view('admin.category.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }

    public function update(UpdateRequest $request, Category $category)
    {
        $category->update($request->all());
        Alert::toast('Categoría actualizada con éxito.', 'success');
        return redirect()->route('categories.index');
        //return redirect()->route('categories.index')->with('toast_success','Categoría actualizada con éxito.');
    }

    public function destroy(Category $category)
    {
        /*$category->delete();
        return redirect()->route('categories.index');*/
    }
}
