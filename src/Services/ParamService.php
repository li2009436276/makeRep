<?php

namespace MakeRep\Services;

class ParamService
{
    /**
     * 构建查询条件
     * @param $request
     * @return array
     */
    public static function createCondition($request){

        $where = [];
        $param = $request->all();

        foreach ($param as $key=>$value) {

            if (!empty($value['method'])) {

                switch ($value['method']) {

                    case "like" : {

                        $where[] = [$key,'like','%'.$value['value'].'%'];
                        break;
                    }
                }
            } else {

                if (is_array($value)) {

                    continue;
                }
                $where[$key] = $value;
            }

        }

        return $where;
    }
}