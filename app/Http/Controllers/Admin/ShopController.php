<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Repositories\Product\ProductRepository;
use App\Http\Repositories\Product\ProductGalleryRepository;

class ShopController extends Controller
{
    protected $productRepo;
    protected $productGalleryRepo;

    public function __construct(ProductRepository $productRepo, ProductGalleryRepository $productGalleryRepo)
    {
        $this->productRepo = $productRepo;
        $this->productGalleryRepo = $productGalleryRepo; 
    }

    public function index()
    {
        $products = Product::where('status', 'ACTIVE')->get(); // Fetching active products
        $cart = Cart::where('user_id', auth()->id())->with('product')->get();
        return view('Shop.product-list', compact('products', 'cart')); // Corrected variable and view reference
    }

    public function showProductDetails($id)
    {
        $product = Product::with('productCategory','productGallery')->findOrFail($id);

        $cart = Cart::where('user_id', auth()->id())->with('product')->get();
        return view('Shop.product-details', compact('product', 'cart'));
    }
}
