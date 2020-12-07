<?php


namespace MakeRep;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container as App;
abstract class Repository
{
    private $app;

    protected $model;

    public function __construct(App $app) {
        $this->app = $app;
        $this->makeModel();
    }

    abstract function model();

    public function makeModel() {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model)
            throw new RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");

        return $this->model = $model;
    }

    /**
     * 添加新数据
     * @param $data
     * @return mixed
     */
    public function add($data){

        $res = $this->model
            ->create($data);
        return $res;
    }

    /**
     * id查询
     * @param $id
     * @return mixed
     */
    public function findById($id){

        $res = $this->model
            ->where('id',$id)
            ->first();
        return $res;
    }

    /**
     * 查询字段
     * @param $where
     * @param string $field
     * @return mixed
     */
    public function index($where,$field = '*'){

        $res = $this->model
            ->where($where)
            ->select($field)
            ->first();
        return $res;
    }

    /**
     * 更新字段
     * @param $where
     * @param $data
     * @return mixed
     */
    public function update($where,$data){

        $res = $this->model
            ->where($where)
            ->update($data);
        return $res;
    }

    /**
     * 批量查询
     * @param $where
     * @param string $field
     * @return mixed
     */
    public function get($where = [],$field = '*'){

        $res = $this->model
            ->where($where)
            ->select($field)
            ->get();
        return $res;
    }

    /**
     * 分页数据
     * @param array $where
     * @param string $field
     * @param int $pageSize
     * @return mixed
     */
    public function pageLists($where = [], $field = '*', $pageSize = 10)
    {
        $res = $this->model
            ->where($where)
            ->select($field)
            ->paginate($pageSize);

        return $res;
    }
}