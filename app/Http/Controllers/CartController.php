<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;

class CartController extends Controller
{

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'nullable|integer|min:1'
        ]);

        $quantity = $request->quantity ?? 1; // default = 1

        // Get product
        $product = Product::where('id', $request->product_id)
            ->where('status', 1)
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not available'], 404);
        }

        // Check existing cart
        $cart = Cart::where('product_id', $product->id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if ($cart) {
            // Increase quantity dynamically
            $cart->quantity += $quantity;
            $cart->price = $product->price; // keep latest price
            $cart->updated_by = 1;
            $cart->save();
        } else {
            // Add new cart row
            Cart::create([
                'product_id' => $product->id,
                'quantity'   => $quantity,
                'price'      => $product->price,
                'status'     => 1,
                'created_by' => 1
            ]);
        }

        return response()->json(['message' => 'Product added to cart successfully']);
    }


    /**
     * View cart
     */
    public function viewCart()
    {
        return response()->json(
            Cart::with('product')
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->get()
        );
    }

    /**
     * Update cart quantity (POST)
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'cart_id'  => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::findOrFail($request->cart_id);

        $cart->quantity   = $request->quantity;
        $cart->updated_by = 1;
        $cart->save();

        return response()->json(['message' => 'Cart updated successfully']);
    }

    /**
     * Remove cart item (Soft delete + status=0)
     */
    public function removeFromCart(Request $request)
    {
        $cart = Cart::findOrFail($request->cart_id);

        $cart->status = 0;
        $cart->updated_by = 1;
        $cart->save();

        $cart->delete();

        return response()->json(['message' => 'Cart item removed']);
    }

    /**
     * Clear cart
     */
    public function clearCart()
    {
        Cart::where('status', 1)->get()->each(function ($cart) {
            $cart->status = 0;
            $cart->updated_by = 1;
            $cart->save();
            $cart->delete();
        });

        return response()->json(['message' => 'Cart cleared successfully']);
    }
}
