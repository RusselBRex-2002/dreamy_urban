<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GeneralResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return ([
            'status' => 'success',
            'code' => isset($this['code']) ? $this['code'] : 200,
            'message' => isset($this['message']) ? $this['message'] :'',
            'data' => isset($this['data']) ? $this['data'] : [],
            'toast' => isset($this['toast']) ? $this['toast'] : false,
        ]);
    }
}
