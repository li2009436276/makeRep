<?php

namespace MakeRep\Services;

class ParamService
{
    /**
     * 构建查询条件
     * @param $request
     * @return array
     */
    public static function createCondition($request,$fillable = []){

        $where = [];
        $param = $request->all();

        foreach ($param as $key=>$value) {

            if (!empty($value['method'])) {

                self::whereParam($value['method'],$key,$value,$where);

            } else if (strpos($key,'#')) {

                if ($value) {

                    $whereStrArray = explode('#',$key);

                    self::whereParam($whereStrArray[1],$whereStrArray[0],$value,$where);
                }


            } else {

                if (is_array($value)) {

                    continue;
                }
                if ($fillable  && $value) {

                    /**
                     * 正常的字段名
                     */
                    if (in_array($key,$fillable)) {

                        $where[$key] = $value;
                    }

                    //将某个字段值转到另外一个字段
                    if (key_exists($key,$fillable)) {

                        $where[$fillable[$key]] = $value;
                    }
                }

            }

        }

        return $where;
    }

    /**
     * where的查询条件
     * @param $method
     * @param $key
     * @param $value
     * @param $where
     * @return void
     */
    protected static function whereParam($method,$key,$value,&$where) {

        switch ($method) {

            case "like" : {

                $where[] = [$key,'like','%'.$value.'%'];
                break;
            }
        }
    }
}