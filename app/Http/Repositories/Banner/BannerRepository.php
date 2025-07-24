<?php


namespace App\Http\Repositories\Banner;

use App\Http\Repositories\Repository;
use App\Models\Banner;
use Illuminate\Support\Facades\Log;

class BannerRepository extends Repository
{
    protected $model;
    protected $model_name = Banner::class;

    public function updateOrCreateData($id, $data)
    {
        return $this->model::updateOrCreate(
            ['id' => $id],
            $data,
        );

    }
}
