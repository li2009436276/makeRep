<?php

namespace MakeRep\Controllers;

use Illuminate\Http\Request;
use MakeRep\Resources\BaseCollection;
use MakeRep\Resources\BaseResource;
use MakeRep\Resources\ErrorResource;
use MakeRep\Services\ParamService;
use DB;

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

        $field = $this->interface->fillable;
        $data = $request->only($field);
        if (in_array('user_id',$field) && empty($request->user_id) && !empty($request->ticket)) {

            $data['user_id'] = $request->ticket['id'];
        }
        return $data;

    }

    /**
     * 获取view地址
     * @return mixed
     */
    protected function getView(){
       return $this->interface->view;
    }

    /**
     * 获取属于
     * @return mixed
     */
    protected function getBelongToRelation(){

        return empty($this->interface->belongToRelation) ? null : $this->interface->belongToRelation;
    }

    /**
     * 添加页面
     * @return mixed
     */
    public function add(){



        $viewDir = $this->getView();

        $belongToRelation = $this->getBelongToRelation();
        if ($belongToRelation) {

            $interface = resolve($belongToRelation[0]);
            $belongToRelationArray = $interface->get($belongToRelation[1]);

            return view($viewDir.'.add',[$belongToRelation[2]=>$belongToRelationArray]);
        }

        return view($viewDir.'.add');

    }

    /**
     * 添加
     * @param Request $request
     * @return BaseResource|ErrorResource
     */
    public function ajaxAdd(Request $request){

        $data = $this->getModelField($request);
        $res = $this->interface->add($data);
        if ($res) {

            return new BaseResource($res);
        }

        return new ErrorResource([]);
    }

    /**
     * 列表
     * @return mixed
     */
    public function lists(){

        $viewDir = $this->getView();
        return view($viewDir.'.lists');
    }

    /**
     * 列表
     * @param Request $request
     * @return BaseCollection
     */
    public function ajaxLists(Request $request){

        $pageSize = $request->page_size ? : 10;
        $orderBy = $request->order_by ? : 'desc';
        $res = $this->interface->pageLists(ParamService::createCondition($request,isset($this->interface->fillable) ? $this->interface->fillable : []),'*',$pageSize,'id',$orderBy);
        return new BaseCollection($res);
    }

    /**
     * 详情
     * @param Request $request
     * @return BaseResource|ErrorResource
     */
    public function update(Request $request){

        $where = $request->id ? ['id'=>$request->id] : [];
        $res = $this->interface->index($where);
        if ($res) {

            $viewDir = $this->getView();

            $belongToRelation = $this->getBelongToRelation();
            if ($belongToRelation) {

                $interface = resolve($belongToRelation[0]);
                $belongToRelationArray = $interface->get($belongToRelation[1]);

                return view($viewDir.'.update',[$res,$belongToRelation[2]=>$belongToRelationArray]);
            }


            return view($viewDir.'.update',$res);
        }

        abort(403);
    }

    /**
     * 更新
     * @param Request $request
     * @return BaseResource|ErrorResource
     */
    public function ajaxUpdate(Request $request){
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

        $where = ParamService::createCondition($request,isset($this->interface->fillable) ? $this->interface->fillable : []);
        if (empty($where)) {

            $where['id'] = $request->ticket['id'];
        }
        $res = $this->interface->index($where);
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
    public function index(Request $request){

        $where = ParamService::createCondition($request,isset($this->interface->fillable) ? $this->interface->fillable : []);
        if (empty($where)) {

            $where['id'] = $request->ticket['id'];
        }
        $res = $this->interface->index($where);
        if ($res) {

            $viewDir = $this->getView();
            return view($viewDir.'.index',$res);
        }

        abort(403);
    }

    /**
     * 删除
     * @param Request $request
     * @return BaseResource|ErrorResource
     */
    public function ajaxDelete(Request $request){

        $where = $request->id ? ['id'=>$request->id] : ['id'=> -1];
        $res = $this->interface->delete($where);
        if ($res) {

            return new BaseResource([]);
        }

        return new ErrorResource([]);

    }

    /**
     * 排序
     * @param Request $request
     * @return \App\Http\Resources\BaseResource|\App\Http\Resources\ErrorResource
     */
    public function ajaxSort(Request $request) {

        $data = $request->all();
        DB::beginTransaction();
        foreach ($data as $value){

            if (is_array($value)) {
                $res = $this->interface->update(['id'=>$value['id']],['sort'=>$value['sort']]);
                if ($res === false) {

                    DB::rollback();
                    return new ErrorResource([]);
                }
            }
        }

        DB::commit();
        return new BaseResource([]);
    }
}