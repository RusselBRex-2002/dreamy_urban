<?php


namespace App\Http\Repositories\Product;

use App\Http\Repositories\Repository;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProjectRepository extends Repository
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
}
