<?php


namespace App\Http\Repositories\Product;

use App\Http\Repositories\Repository;
use App\Models\ProductGallery;
use Illuminate\Support\Facades\Log;

class ProductGalleryRepository extends Repository
{
    protected $model;
    protected $model_name = ProductGallery::class;

    public function updateOrCreateData($id, $data)
    {
        return $this->model::updateOrCreate(
            ['id' => $id],
            $data,
        );
    }
}
