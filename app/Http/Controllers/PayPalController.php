<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    //
    // public function index()
    // {
    //     return view('paypal');
    // }

    public function payment(Request $request)
    {
        $cart = Cart::where('user_id', auth()->id())->with('product')->get();
        $subtotal = 0;
        // Calculate the subtotal
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item->quantity * $item->product->price;
        }
        $shippingCost = 5.00; // Set a default shipping cost

        $total = $subtotal + $shippingCost;

        $firstName = $request->query('first_name');
        $lastName = $request->query('last_name');
        $shippingAddress = $request->query('shipping_address');
        $paymentMethod = $request->query('payment_method');

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

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        
        
        
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('paypal.payment.success'),
                "cancel_url" => route('paypal.payment/cancel'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $total
                    ]
                ]
            ]
        ]);
  
        if (isset($response['id']) && $response['id'] != null) {
  
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }
  
            return redirect()
                ->route('cancel.payment')
                ->with('error', 'Something went wrong.');
  
        } else {
            return redirect()
                // ->route('create.payment')
                ->with('error', $response['message'] ?? 'Something went wrong.');
        }
    
    }

    public function paymentCancel()
    {
        return redirect()
              ->route('paypal')
              ->with('error', $response['message'] ?? 'You have canceled the transaction.');
    }

    public function paymentSuccess(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);
  
        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                $order = Order::where('user_id', Auth::id())
                          ->where('status', 'pending')
                          ->latest()
                          ->first(); // Assuming you want to update the latest order
                if ($order) {
                    $order->status = 'completed'; // Update order status to completed
                    $order->save();
                }

                // Clear the cart
                Cart::where('user_id', Auth::id())->delete();
            return redirect()
                ->route('shop.index')
                ->with('success', 'Transaction complete.');
        } else {
            $order = Order::where('user_id', Auth::id())
                          ->where('status', 'pending')
                          ->latest()
                          ->first();

            if ($order) {
                $order->status = 'failed'; // Mark order as failed
                $order->save();
            }
            return redirect()
                ->route('placeOrder')
                ->with('error', $response['message'] ?? 'Something went wrong.');
        }
    }
}
