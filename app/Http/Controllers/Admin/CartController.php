<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    public function addToCart(Request $request) {
     
        $product = Product::findOrFail($request->product_id);
        $user_id = auth()->id();  // Assume user is authenticated

        // Check if product is already in cart
        $cartItem = Cart::where('user_id', $user_id)->where('product_id', $request->product_id)->first();

        if ($cartItem) {
            // Update quantity if already in cart
            $cartItem->quantity += $request->quantity;
            $cartItem->total_price = $cartItem->quantity * $cartItem->price; // Recalculate total price
            $cartItem->save();
        } else {
            // Add new product to cart
            Cart::create([
                'user_id' => $user_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->price,
                'total_price' => $product->price * $request->quantity,
            ]);
        }
        return response()->json([
            'message' => 'Product added to cart successfully!',
            'product' => $product // Optionally include product data
        ]);
        //return redirect()->back()->with('success', 'Product added to cart successfully');
    }

    public function showCart() {
        $cart = Cart::where('user_id', auth()->id())->with('product')->get();
        $total = 0;
        foreach ($cart as $item) {
           $total += $item->quantity * $item->product->price;
        }

        return view('Shop.cart', compact('cart','total'));
    }

    public function removeCartItem($id) {
        Cart::where('id', $id)->where('user_id', auth()->id())->delete();
        return redirect()->back()->with('success', 'Product removed from cart');
    }

    public function updateCart(Request $request) {
        foreach ($request->cart as $item) {
            Cart::where('id', $item['id'])->where('user_id', auth()->id())->update([
                'quantity' => $item['quantity'],
                'total_price' => $item['quantity'] * $item['price'],
            ]);
        }
        return redirect()->back()->with('success', 'Cart updated successfully');
    }

    public function showSidebarCart()
    {
        // Fetch cart data
        $cart = Cart::getContent(); // Adjust based on your cart implementation

        return view('components.sidebar-cart', compact('cart'));
    }
    
    
}
