<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'banners';

    protected $fillable = [
        'image','background_image','title','description','link','status'
    ];

    protected $appends = ['full_image','full_background_image'];

    public function getFullImageAttribute()
    {
        if (!empty($this->image)) {
            return url(Storage::url($this->image));
        }
        return null;
    }

    public function getFullBackgroundImageAttribute()
    {
        if (!empty($this->background_image)) {
            return url(Storage::url($this->background_image));
        }
        return null;
    }
}
