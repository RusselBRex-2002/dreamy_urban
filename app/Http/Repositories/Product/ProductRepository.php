<?php


namespace App\Http\Repositories\Product;

use App\Http\Repositories\Repository;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductRepository extends Repository
{
    protected $model;
    protected $model_name = Product::class;

    public function updateOrCreateData($id, $data)
    {
        return $this->model::updateOrCreate(
            ['id' => $id],
            $data,
        );
    }

    public function products()
    {
        return $this->model->with('productCategory','productGallery','productSpec')->get();
    }

    public function getPopularProduct()
    {
        return $this->model->where('best_seller','YES')->with('productCategory','productGallery','productSpec')->get();
    }

    public function getAllDetailProduct($id)
    {
        return $this->model->where('id',$id)->with('productCategory','productGallery','productSpec')->first();
    }

    public function getRelatedProject($id)
    {
        return $this->model->where('id','!=',$id)->with('productCategory','productGallery','productSpec')->get();
    }
}
