<?php


namespace App\Http\Repositories\User;

use App\Http\Repositories\Repository;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserRepository extends Repository
{
    protected $model;
    protected $model_name = User::class;
    

    public function getByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }
    
    public function getByPhone($phone_number)
    {
        return $this->model->where('phone',$phone_number)->first();
    }

}
