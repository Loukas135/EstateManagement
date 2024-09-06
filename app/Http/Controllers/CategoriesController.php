<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json([
            'message' => 'All Categories',
            'data' => $categories
        ], 200);
    }

    public function getEstateCategories()
    {
        $categories = Category::where('cat_type', 'Estates')->get();
        return response()->json([
            'message' => 'Estate Categories',
            'data' => $categories
        ], 200);
    }

    public function getExtraCategories()
    {
        $categories = Category::where('cat_type', 'Services')->get();
        return response()->json([
            'message' => 'Extra Categories',
            'data' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cat_type' => 'required|in:Estates,Services',
        ]);

        $category = Category::create($request->all());
        return response()->json($category, 201);
    }


    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'cat_type' => 'sometimes|required|in:Estates,Services',
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->all());
        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(null, 204);
    }
}
