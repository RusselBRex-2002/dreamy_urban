<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GeneralError extends JsonResource
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
            'status' => 'error',
            'code' => isset($this['code']) ? $this['code'] : 400,
            'message' => $this['message'],
            'data' => isset($this['data']) ? $this['data'] : [],
            'toast' => isset($this['toast']) ? $this['toast'] : false,
        ]);
    }

    public function withResponse($request, $response)
    {
        return ($response)->setStatusCode(200);
    }
}
