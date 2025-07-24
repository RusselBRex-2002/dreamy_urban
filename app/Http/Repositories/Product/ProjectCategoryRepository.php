<?php


namespace App\Http\Repositories\Product;

use App\Http\Repositories\Repository;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Log;

class ProjectCategoryRepository extends Repository
{
    protected $model;
    protected $model_name = ProductCategory::class;

    public function updateOrCreateData($id, $data)
    {
        return $this->model::updateOrCreate(
            ['id' => $id],
            $data,
        );
    }

    public function categoryWiseProduct()
    {
        return $this->model->with(['products' => function($qry) {
            $qry->with('productGallery')->get();
        }] )->get();
    }
}
