<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class Blog extends Model
{
    use HasFactory;

    protected $table = 'blogs';

    protected $fillable = [
        'hero_image','image','banner_image','title','description','helight_description','date','meta_title','meta_description'
    ];

    protected $appends = ['full_hero_image','full_image','full_banner_image'];

    public function getFullHeroImageAttribute()
    {
        if (!empty($this->hero_image)) {
            return url(Storage::url($this->hero_image));
        }
        return null;
    }

    public function getFullImageAttribute()
    {
        if (!empty($this->image)) {
            return url(Storage::url($this->image));
        }
        return null;
    }

    public function getFullBannerImageAttribute()
    {
        if (!empty($this->banner_image)) {
            return url(Storage::url($this->banner_image));
        }
        return null;
    }
}
