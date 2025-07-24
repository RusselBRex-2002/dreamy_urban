<?php

namespace App\Http\Repositories\Coupon;

use App\Http\Repositories\Repository;
use App\Models\Coupon;
use Illuminate\Support\Facades\Log;

class CouponRepository extends Repository
{
    protected $model;
    protected $model_name = Coupon::class;

    public function updateOrCreateData($id, $data)
    {
        return $this->model::updateOrCreate(
            ['id' => $id],
            $data,
        );
    }
}
