<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{

    public function index(Request $request){
        $cart = Cart::where('user_id', auth()->id())->with('product')->get();
        $subtotal = 0;
        foreach ($cart as $item) {
           $subtotal += $item->quantity * $item->product->price;
        }
        $shippingCost = 5.00;
        $total = $subtotal + $shippingCost;

        return view('Shop.checkout', compact('cart','subtotal','shippingCost','total'));
    }

    public function placeOrder(Request $request){

        $cart = Cart::where('user_id', auth()->id())->with('product')->get();
        $subtotal = 0;
        // Calculate the subtotal
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item->quantity * $item->product->price;
        }
        $shippingCost = 5.00; // Set a default shipping cost

        $total = $subtotal + $shippingCost;

        // Create a new order
        $order = new Order();
        $order->user_id = Auth::id();
        $order->first_name = $request->input('first_name'); // Added first_name
        $order->last_name = $request->input('last_name'); // Added last_name
        $order->shipping_address = $request->input('shipping_address');
        $order->subtotal = $subtotal;
        $order->shipping_cost = $shippingCost;
        $order->total = $total;
        $order->payment_method = $request->input('payment_method');
        $order->status = 'pending';
        $order->save();

        // Save cart items in the order_items table (optional)
        foreach ($cart as $item) {
            $order->orderItems()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        // Clear the cart
        Cart::where('user_id', Auth::id())->delete();

        return redirect()->route('shop.index')->with('message', 'Order placed successfully');
    }
}
