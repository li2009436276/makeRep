<?php

namespace MakeRep\Controllers;

use Illuminate\Http\Request;
use MakeRep\Resources\BaseCollection;
use MakeRep\Resources\BaseResource;
use MakeRep\Resources\ErrorResource;

class BaseController
{
    protected $interface;
    public function __construct($interface)
    {
        $this->interface = $interface;
    }

    /**
     * 获取请求数据
     * @param $request
     * @return mixed
     */
    protected function getModelField($request){

        $field = $this->interface->model->get('fillable');
        $data = $request->only($field);
        if (in_array('user_id',$field)) {

            $data['user_id'] = $request->ticket['id'];
        }

        return $data;
    }

    /**
     * 添加
     * @param Request $request
     * @return BaseResource|ErrorResource
     */
    public function add(Request $request){

        $data = $this->getModelField($request);
        $res = $this->interface->add($data);
        if ($res) {

            return new BaseResource($res);
        }

        return new ErrorResource([]);
    }

    /**
     * 列表
     * @param Request $request
     * @return BaseCollection
     */
    public function lists(Request $request){

        $pageSize = $request->page_size ? : 10;
        $orderBy = $request->order_by ? : 'desc';
        $res = $this->interface->pageLists([],'*',$pageSize,'id',$orderBy);
        return new BaseCollection($res);
    }

    /**
     * 更新
     * @param Request $request
     * @return BaseResource|ErrorResource
     */
    public function update(Request $request){
        $data = $this->getModelField($request);
        $where = $request->id ? ['id'=>$request->id] : [];
        $res = $this->interface->update($where,$data);
        if ($res) {

            return new BaseResource($res);
        }

        return new ErrorResource([]);
    }

    /**
     * 详情
     * @param Request $request
     * @return BaseResource|ErrorResource
     */
    public function info(Request $request){

        $where = $request->id ? ['id'=>$request->id] : [];
        $res = $this->interface->index($where,$request->all());
        if ($res) {

            return new BaseResource($res);
        }

        return new ErrorResource([]);
    }

    /**
     * 删除
     * @param Request $request
     * @return BaseResource|ErrorResource
     */
    public function delete(Request $request){

        $where = $request->id ? ['id'=>$request->id] : [];
        $res = $this->interface->delete($where);
        if ($res) {

            return new BaseResource($res);
        }

        return new ErrorResource([]);

    }
}