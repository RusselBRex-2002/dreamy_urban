<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';

    protected $fillable = [
        'title','category','banner_image','price','offer_price','discount_amount','sku','no_of_qty','description','information','color','size','coupon','meta_keyword','meta_title','meta_description','status'
    ];

    protected $appends = ['full_banner_image'];

    public function getFullBannerImageAttribute()
    {
        if (!empty($this->banner_image)) {
            return url(Storage::url($this->banner_image));
        }
        return null;
    }

    public function productCategory()
    {
        return $this->hasOne(ProductCategory::class,'id','category');
    }

    public function productGallery()
    {
        return $this->hasMany(ProductGallery::class,'product_id','id');
    }
}
