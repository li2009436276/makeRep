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
     * 条件查询
     * @param $where
     * @return mixed
     */
    public function find($where){

        $res = $this->model
            ->where($where)
            ->first();
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
}