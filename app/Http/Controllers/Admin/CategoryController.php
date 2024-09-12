<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    //
    public function index()
    {
        $categories = Category::all();

        return response()->json(['data' => $categories], 200);
    }

    public function sortCategories()
    {
        $categories = Category::orderBy('sort', 'desc')->take(8)->get();

        return response()->json(['data' => $categories], 200);
    }

    public function getCategory($id)
    {
        $category = Category::findOrFail($id);

        return response()->json(['data' => $category], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'is_show' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 402);
        }
        $category = Category::create($request->all());

        // $category = Category::findOrFail($id);
        return response()->json(['data' => $category], 201);
    }

    public function updateCategory(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 402);
        }
        $category = Category::findOrFail($id);
        $category->update($request->all());

        return response()->noContent();
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        if (! $category) {
            return response()->json([
                'message' => 'Category not found',
            ], 404);
        }
        $category->delete();

        return response()->noContent();
    }
}
