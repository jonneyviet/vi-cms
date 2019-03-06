<?php
namespace Packages\Translation\Repositories;
/**
* 
*/
class Repository
{
	public function getModel(){
		return $this->model;
	}
	public function tableExists()
    {
        return $this->model->getConnection()->getSchemaBuilder()->hasTable($this->model->getTable());//check table exit database
    }
    //get all recode and paginate if $perPage different 0;
    public function all($related = [], $perPage = 0){
    	$result = $this->model->with($related)->order_by("created_at","DESC");
    	return $perPage ? $result->paginate($perPage) : $results->get();
    }
    //Retrieve all trashed.
    public function transhed($related = [], $perPage = 0){
    	$result = $this->model->onlyTranshed()->with($related);
    	return $perPage ? $result->paginate($perPage) : $results->get();
    }
    public function find($id,$related = []){
    	return $this->model->with($related)->find($id);
    }
    public function findTranshed($id,$related = []){
    	return $this->model->onlyTranshed()->with($related)->find($id);
    }
    public function delete($id){
    	$model=$this->model->where("id",$id)->first();
    	if(!$model){
    		return false;
    	}
    	$model->delete();
    }
    public function restore($id){
    	$model=$this->model->findTranshed($id);
    	if($model){
    		$model->restore();
    	}
    	return $model;
    }
    public function count(){
    	return $this->model->count();
    }
}