<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $fillable = [
        'user_id', 'product_id', 'quantity', 'price', 'total_price'
    ];
    // Relationship to the product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    // Relationship to the user (if you want to associate carts with logged-in users)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
