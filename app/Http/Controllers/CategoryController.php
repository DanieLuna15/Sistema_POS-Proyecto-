<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Requests\Category\StoreRequest;
use App\Http\Requests\Category\UpdateRequest;

use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $categories = Category::get();
        return view('admin.category.index', compact('categories'));
    }

    ##AJAX request
    /*public function getCategories(Request $request){
        $search = $request->search;
        if($search== ''){
            $employes=Categories::orderby('name',asc)
                        ->select('id','name')
                        ->limit(5)
                        ->get();
        }else{
            $employes=Categories::orderby('name',asc)
                        ->select('id','name')
                        ->where('name','like','%'.$search.'%')
                        ->limit(5)
                        ->get();
        }
        $response=array();
        foreach($categories as $category){
            $response[]=array(
                "id" => $category->id,
                "text" => $category-name
            );
        }
        echo json_encode($response);
        exit;
    }*/

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(StoreRequest $request)
    {
        Category::create($request->all());
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
        return redirect()->route('categories.index');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index');
    }
}
