<?php

namespace MakeRep\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

    /**
     * 获取变量
     * @param $variable
     * @return mixed
     */
    public function __get($variable)
    {
        return $this->$variable;
    }
}