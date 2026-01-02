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
        $products = Product::where('status', 1)
            ->whereNull('deleted_at')
            ->get();

        return response()->json($products);
    }

    /**
     * Add new product
     */
    public function addProduct(Request $request)
    {
        $product = Product::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'status'      => 1,
            'created_by'  => 1
        ]);

        return response()->json([
            'message' => 'Product added successfully',
            'data'    => $product
        ]);
    }

     /**
     * Update existing product
     */
    public function updateProduct(Request $request)
    {
        // Find the product by ID from request body
        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Update product details
        $product->name        = $request->name ?? $product->name;
        $product->description = $request->description ?? $product->description;
        $product->price       = $request->price ?? $product->price;
        $product->updated_by  = 1;
        $product->save();

        return response()->json([
            'message' => 'Product updated successfully',
            'data'    => $product
        ]);
    }

    /**
     * Soft delete product & update status
     */
    public function deleteProduct(Request $request)
    {
        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->status = 0;
        $product->updated_by = 1;
        $product->save();
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
}
