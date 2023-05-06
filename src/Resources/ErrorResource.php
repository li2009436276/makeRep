<?php

namespace MakeRep\Resources;


use Illuminate\Http\Resources\Json\Resource;
class ErrorResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [

        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        $code = 'FAIL';
        if (is_string($this->resource)) {

            $code = $this->resource;
        }

        list($code,$msg) = config("code.{$code}");
        return [
            'errcode' => $code,
            'errmsg' => !empty($this->resource['msg']) ? $this->resource['msg']: $msg
        ];
    }
}