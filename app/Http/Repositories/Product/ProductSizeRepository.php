<?php


namespace App\Http\Repositories\Product;

use App\Http\Repositories\Repository;
use App\Models\ProductSize;
use Illuminate\Support\Facades\Log;

class ProductSizeRepository extends Repository
{
    protected $model;
    protected $model_name = ProductSize::class;

    public function updateOrCreateData($id, $data)
    {
        return $this->model::updateOrCreate(
            ['id' => $id],
            $data,
        );
    }
}
