<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_categories';

    protected $fillable = [
        'name', 'image', 'status'
    ];

    protected $appends = ['full_image'];

    public function getFullImageAttribute()
    {
        if (!empty($this->image)) {
            return url(Storage::url($this->image));
        }
        return null;
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category', 'id');
    }
}
