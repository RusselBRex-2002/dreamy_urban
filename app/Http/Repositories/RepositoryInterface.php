<?php

namespace App\Http\Repositories;

interface RepositoryInterface
{
    public function create(array $data);

    public function getById($id);

    public function update($id, array $data);

    public function delete($id);

    public function deleteAll(array $ids);

    public function all();
}