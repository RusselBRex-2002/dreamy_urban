<?php


namespace App\Http\Repositories\Blog;

use App\Http\Repositories\Repository;
use App\Models\Blog;
use Illuminate\Support\Facades\Log;

class BlogRepository extends Repository
{
    protected $model;
    protected $model_name = Blog::class;

    public function updateOrCreateData($id, $data)
    {
        return $this->model::updateOrCreate(
            ['id' => $id],
            $data,
        );

    }
}
