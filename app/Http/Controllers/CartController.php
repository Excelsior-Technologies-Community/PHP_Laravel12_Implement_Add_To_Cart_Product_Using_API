<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;

class CartController extends Controller
{
    /**
     * Add product to cart
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $quantity = $request->quantity ?? 1;

        $product = Product::where('id', $request->product_id)
            ->where('status', 1)
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not available'], 404);
        }

        $cart = Cart::where('product_id', $product->id)
            ->where('status', 1)
            ->first();

        if ($cart) {
            $cart->quantity += $quantity;
            $cart->price = $product->price;
            $cart->updated_by = 1;
            $cart->save();
        } else {
            Cart::create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
                'status' => 1,
                'created_by' => 1
            ]);
        }

        return response()->json(['message' => 'Product added to cart successfully']);
    }

    /**
     * View cart items
     */
    public function viewCart()
    {
        $cartItems = Cart::with('product')
            ->where('status', 1)
            ->get();

        return response()->json($cartItems);
    }

    /**
     * Update cart quantity
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|integer|exists:carts,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::find($request->cart_id);
        $cart->quantity = $request->quantity;
        $cart->updated_by = 1;
        $cart->save();

        return response()->json(['message' => 'Cart updated successfully']);
    }

    /**
     * Remove single cart item
     */
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|integer|exists:carts,id'
        ]);

        $cart = Cart::find($request->cart_id);
        $cart->status = 0;
        $cart->updated_by = 1;
        $cart->save();
        $cart->delete();

        return response()->json(['message' => 'Cart item removed']);
    }

    /**
     * Clear entire cart
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