<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // public function index()
    // {
    //     $products = Products::with(['images', 'category'])->get();
    //     return response()->json($products);
    // }

    public function index(Request $request)
    {
        $query = Products::with(['images', 'category']);

        // Tìm kiếm theo tên sản phẩm
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->query('search') . '%');
        }

        // Lọc theo danh mục nếu có
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        // Phân trang
        $perPage = $request->query('perPage', 10);
        $products = $query->paginate($perPage);

        return response()->json($products);
    }



    public function show($id)
    {
        $product = Products::with(['images', 'category'])->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20048',
            'main_image_index' => 'required|integer|min:0'
        ]);

        $product = new Products;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('images', 'public');

                $product->images()->create([
                    'path' => $path,
                    'is_main' => $index == $request->main_image_index,
                    'product_id' => $product->id
                ]);
            }
        }

        return response()->json(['message' => 'Product created successfully'], 201);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Products::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->name = $request->name;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->save();

        if ($request->has('existing_images')) {
            $product->images()->delete();

            foreach ($request->existing_images as $imageData) {
                $product->images()->create([
                    'path' => $imageData['path'],
                    'is_main' => $imageData['is_main']
                ]);
            }
        }

        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $path = $image->store('images', 'public');

                $product->images()->create([
                    'path' => $path,
                    'is_main' => 0
                ]);
            }
        }
        return response()->json(['message' => 'Product updated successfully'], 200);
    }

    public function destroy($id)
    {
        $product = Products::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        foreach ($product->images as $image) {
            Storage::delete('public/' . $image->path);
        }
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function showByCategory($categoryId)
    {
        $products = Products::where('category_id', $categoryId)
            ->with('category', 'images')
            ->get();

        return response()->json($products, 200);
    }
}
