<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\Banner;
use App\Models\Blog;

class HomeController extends Controller
{
    public function index()
    {

        $banners = Banner::where('status','ACTIVE')->get();
        $bannerCategories = ProductCategory::where('status','ACTIVE')->take(3)->get();
        $categories = ProductCategory::with(['products' => function ($query) {
            $query->limit(8);
        }])->limit(3)->get();

        // You can pass any data to the view if needed
        return view('welcome',compact('categories','banners','bannerCategories')); // This will return the 'welcome' view
    }
    
    public function contactUs()
    {
        return view('contact');
    }
    
    public function blogs()
    {
        $blogs = Blog::all();
        return view('blog',compact('blogs'));
    }

    public function blogDetail($title,Request $request)
    {
        $formatedBlogTitle = str_replace('_', ' ', $title);
        $blogDetail =  Blog::where('title',$formatedBlogTitle)->first();
        return view('blogDetail',compact('blogDetail'));
    }
}
