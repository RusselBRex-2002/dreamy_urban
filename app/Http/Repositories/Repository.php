<?php

namespace App\Http\Repositories;

class Repository implements RepositoryInterface
{
    protected $model;

    protected $model_name = '';

    public function __construct()
    {
        $this->model = new $this->model_name;
    }

    public function create(array $inputs)
    {
        return $this->model->create($inputs);
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function update($id, array $inputs)
    {
        return tap($this->model->find($id))->update($inputs)->fresh();
    }

    public function delete($id)
    {
        return $this->getById($id)->delete();
    }

    public function deleteAll(array $ids)
    {
        return $this->model->destroy($ids);
    }

    public function all()
    {
        return $this->model->all();
    }

    static function forceDeleteByModel($modelName, $whereIn = [], $where = [])
    {
        $query = new $modelName;
        if (!empty($where)) {
            $query = $query->where($where);
        }

        if (!empty($whereIn)) {
            foreach ($whereIn as $column => $array) {
                $query = $query->whereIn($column, $array);
            }
        }

        return $query->forceDelete();
    }

    public function updateOrCreate($id, array $inputs)
    {
        return $this->model->updateOrCreate($id, $inputs);
    }

}
