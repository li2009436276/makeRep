<?php

namespace MakeRep\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
class BaseCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return $this->resource;
    }

    public function with($request){

        return [
            'errcode' => 0,
            'errmsg' => '成功'
        ];

    }
}