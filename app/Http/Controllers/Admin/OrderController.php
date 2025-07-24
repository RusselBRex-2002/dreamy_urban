<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cart;
use DataTables, DB, Log;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Repositories\Order\OrderRepository;

class OrderController extends Controller
{

    protected $orderRepo;

    public function __construct(orderRepository $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    public function index(Request $request){
        return view('Admin.Order.index');
    }
    public function getOrderList(Request $request)
    {
        try {
            $data = Order::with('orderItems')->get();
            return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }
}
