<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(){

        $categories  = Category::all();

        return view('categories.index', compact('categories'));
    }
    public function create(){
        return view('categories.create');
    }
    public function store(Request $request){

        Category::create($request->all());
        return redirect()->route('categories.index');
    }
    public function edit($id){

        $categorie = Category::findOrFail($id);


        return view('categories.edit',compact('categorie'));

    }
    public function update(Request $request,$id){
        $categorie = Category::findOrFail($id);
        $categorie->update($request->all());
        return redirect()->route('categories.index');

    }
    public function destroy(Request $request,$id){
        $categorie = Category::findOrFail($id);
        $categorie->delete($request->all());
        return redirect()->route('categories.index');

    }
}
