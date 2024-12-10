<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric',
            'discount' => 'required|numeric',
        ]);

        $cart = Cart::create([
            'user_id' => $validated['user_id'],
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'total_price' => $validated['total_price'],
            'discount' => $validated['discount'],
        ]);

        return response()->json($cart, 201);
    }

    public function getCart($userId)
    {
        $cartItems = Cart::where('user_id', $userId)->with('product')->get();
        return response()->json($cartItems);
    }

    public function removeFromCart($cartId)
    {
        $cart = Cart::find($cartId);

        if ($cart) {
            $cart->delete();
            return response()->json(['message' => 'Product removed from cart successfully.']);
        }

        return response()->json(['message' => 'Cart item not found.'], 404);
    }

    public function updateQuantity(Request $request, $cartId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::find($cartId);

        if ($cart) {
            $cart->quantity = $validated['quantity'];
            $cart->save();
            return response()->json($cart);
        }

        return response()->json(['message' => 'Cart item not found.'], 404);
    }

    public function updateDiscount(Request $request, $cartId)
    {
        $validated = $request->validate([
            'discount' => 'required|numeric',
        ]);

        $cart = Cart::find($cartId);

        if ($cart) {
            $cart->discount = $validated['discount'];
            $cart->save();
            return response()->json($cart);
        }

        return response()->json(['message' => 'Cart item not found.'], 404);
    }

    public function clearCart($userId)
    {
        Cart::where('user_id', $userId)->delete();
        return response()->json(['message' => 'Cart cleared successfully.']);
    }
}
