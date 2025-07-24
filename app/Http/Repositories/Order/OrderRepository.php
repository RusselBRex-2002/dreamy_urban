<?php


namespace App\Http\Repositories\Order;

use App\Http\Repositories\Repository;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderRepository extends Repository
{
    protected $model;
    protected $model_name = Order::class;


    public function order()
    {
        return $this->model->with('orderItems')->get();
    }
}
