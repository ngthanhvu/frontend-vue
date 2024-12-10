<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Lấy danh sách danh mục
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    // Lấy chi tiết danh mục
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json($category);
    }

    // Tạo mới danh mục
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $category = new Category;
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();

        return response()->json(['message' => 'Category created successfully'], 201);
    }

    // Cập nhật danh mục
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->name = $request->name;
        $category->save();

        return response()->json(['message' => 'Category updated successfully']);
    }

    // Xóa danh mục
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
