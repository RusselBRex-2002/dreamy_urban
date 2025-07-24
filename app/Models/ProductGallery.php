<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductGallery extends Model
{
    use HasFactory;

    protected $table = 'product_gallery';

    protected $fillable = [
        'product_id', 'image'
    ];

    protected $appends = ['full_image'];

    public function getFullImageAttribute()
    {
        if (!empty($this->image)) {
            return url(Storage::url($this->image));
        }
        return null;
    }
    
}
