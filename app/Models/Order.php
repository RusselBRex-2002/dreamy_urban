<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'mobile_no',
        'shipping_address',
        'billing_address',
        'subtotal',
        'shipping_cost',
        'total',
        'payment_method',
        'country',
        'state',
        'city',
        'pincode',
        'status',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
