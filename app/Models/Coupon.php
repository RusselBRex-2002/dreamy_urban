<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupon';

    protected $fillable = [
        'name', 'type', 'discount', 'expiry_date', 'status'
    ];
}
