<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * List all active products
     */
    public function listProducts()
    {
        $products = Product::where('status', 1)->get();
        return response()->json($products);
    }

    /**
     * Add new product
     */
    public function addProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0'
        ]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'status' => 1,
            'created_by' => 1
        ]);

        return response()->json([
            'message' => 'Product added successfully',
            'data' => $product
        ]);
    }

    /**
     * Update existing product
     */
    public function updateProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0'
        ]);

        $product = Product::find($request->product_id);

        $product->name = $request->name ?? $product->name;
        $product->description = $request->description ?? $product->description;
        $product->price = $request->price ?? $product->price;
        $product->updated_by = 1;
        $product->save();

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    /**
     * Soft delete product
     */
    public function deleteProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id'
        ]);

        $product = Product::find($request->product_id);
        $product->status = 0;
        $product->updated_by = 1;
        $product->save();
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
}