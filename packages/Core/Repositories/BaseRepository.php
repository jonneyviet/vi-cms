<?php

namespace Packages\Core\Repositories;

use Illuminate\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Validator;
use Carbon\Carbon;
use Packages\Media\Repositories\Contracts\RepositoryInterface;

abstract class BaseRepository{

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $_model;


    public function __construct(Application $app)
    {
        $this->setModel();
        $this->app              = $app;
    }
    /**
     * get model
     * @return string
     */
    abstract public function getModel();
    /**
     * Set model
     */
    public function setModel()
    {
        $this->_model = app()->make(
            $this->getModel()
        );
    }
    /**
     * create
     * @param  [id] $attributes [description]
     * @return [int id]             [id return]
     */
    /**
     * Created
     */
    public function create($attributes){
        return $this->validate($attributes)? $this->_model->insertGetId($attributes):null;
    }
    /**
     * get All
     */
    public function all(){
        $results = $this->_model;
        return $results;
    }
    public function lang($lang=null){
        $results = $this->_model;
        return ($lang)? $results->where("lang",$lang): $results;
    }
    public function isPublic($option=null){
        $results = $this->_model;
        switch ($option) {
            case '1':
                return $results->where("is_public",true);
                break;
            case '0':
                return $results->where("is_public",false);
                break;
            default:
                return $results;
                break;
        }
    }
    public function trashed($id){
        return $this->_model->where("id",$id)->update(["deleted_at"=>Carbon::now()]);
    }

    public function validate($attributes){
        return true;
    }

    public function uploadImageAvatar($id,$media_files_id){
        return $this->_model->where("id",$id)->update(["avatar"=>$media_files_id]);
    }
}