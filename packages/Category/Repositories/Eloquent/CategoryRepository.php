<?php

namespace Packages\Category\Repositories\Eloquent;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Application;
use Illuminate\Database\Eloquent\Collection;
use League\Flysystem\Exception;
use Packages\Core\Repositories\BaseRepository;
use Carbon\Carbon;
use Validator;
use Auth;
use Packages\Category\Models\Category;
use Packages\Category\Repositories\Contracts\CategoryRepositoryInterface;


class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface{
     /**
     *  Validator
     *
     *  @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     *  Validation errors.
     *
     *  @var \Illuminate\Support\MessageBag
     */
    protected $errors;

	public function __construct(Application $app)
    {
        parent::__construct($app);
    }
    public function getModel()
    {
        return Category::class;
    }

    public function getItem($key,$option=null){
        $category = $this->convertKey($key);
        if(!$category){
            return false;
        }
        $data=$this->_model->where("id",$category->id);
        if(!is_null($option)){
           $t = explode(",",$option);
           $data=$data->select($t);
        }
        $data=$data->first();
        $created_at=$data->created_at;
        //$created_at=Carbon::instance($data->created_at)->toIso8601String();
        //return data_set($data,"date",$created_at);
        return $data;
    }

    public function search($keyword,$option=null){
        $d=$this->_model;
        if(!is_null($keyword)){
            $d=$this->_model->where("name","like","%{$keyword}%");
        }
        switch ($option) {
            default:
                $data=$d
                    ->select("id","name")
                    ->where("is_public",false)
                    ->orderByRaw('created_at DESC')
                    ->orderByRaw('updated_at DESC');
                break;
        }
        return $data->paginate(config("media.paginate"))->appends(request()->query());
    }

    public function update($key,$request){
	    $category=$this->convertKey($key);
        if(!$category){
           return false;
        }
        $t =[];
        $data=$request->data;
        if(array_has($data,'name')){
            if(!$this->validate($data)){
                return false;
            }
            $t=array_add($t,"name",$data['name']);
        }
        if(array_has($data,'text')){
            $t=array_add($t,"text",$data['text']);
        }
        if(array_has($data,'created_at')){
            $t=array_add($t,"created_at",$data['created_at']);
        }
        if(array_has($data,'is_public')){
            $t=array_add($t,"is_public",$data['is_public']);
        }
        $this->_model->where("id",$category->id)->update($t);
        return $t;
    }

    public function convertId($id){
         return $this->_model->where("id",$id)->first();
    }
    public function showErrors(){

        if(!is_null($this->errors)){
            return response()->json([
                "message"=>($this->errors)
            ],442);
        }
        return $this;
    }

    public function getTypeCategory($id){
	    foreach (config("category.type_category") as $item){
	        if($item["id"]===$id){
	            return $item["name"];
            }
        }
    }
    private function getTextJson($id){
	    return [
	        [
	            'key'=>'01',
	            'title'=>trans("category.default"),
                'content'=>''
            ]
        ];
    }
    // =====================get Data=========================
    public function countChildren($id){
        return $this->_model->find($id)->children()->count();
    }
    public function countPosts($id){
        return $this->_model->find($id)->posts()->count();
    }
    public function  getData($request){
	    $data=$this->_model;
        if($request["key"]){
            $data=$data->where("parent_id",$this->convertKey($request["key"])->id);
        }else{
            $data=$data->where("parent_id",null);
        }
        if($request["lang"]){
            $data=$data->where("lang",$request["lang"]);
        }
        if($request["search"]){
            $data=$data->where("name","like","%{$request["search"]}%");
        }
        if($request["is_public"]){
            $data=$data->where("is_public",$request["is_public"]);
        }
        if($request["type"]){
            $data=$data->where("type",$request["type"]);
        }
        if($request["date"]==="asc"){
            $data=$data->orderByRaw('created_at ASC');
        }else{
            $data=$data->orderByRaw('created_at DESC');
        }
        return $data;
    }

    public function breadcrumb($key){
        $data=[];
	    if(is_null($key)){
	        return $data;
        }
        $category=$this->convertKey($key);
        $id=$category->id;
        if(!$id){ return false;}

        foreach ($this->getListIdParent($id) as $value) {
            $category_db=$this->_model->where("id",$value)->first();
            array_push($data,[
                "key"=>$category_db->key,
                "name"=>str_limit($category_db->name, 30, $end = '...'),
            ]);
        }
        array_push($data,[
            "key"=>$category->key,
            "name"=>str_limit($category->name, 30, $end = '...'),
        ]);
        return $data;
    }
    //=====================Created category=========================

    public function createCategory($request){
	    $data=[
            "key"           =>$this->keyCategory(),
	        "is_public"     =>false,
            "add_by"        =>Auth::user()->id,
            "created_at"    =>Carbon::now(),
        ];
        $lang=(is_null($request['lang']))? config('app.locale'):$request["lang"];
        $data=array_add($data,"lang",$lang);

        $type=(is_null($request['type']))? 1: $request["type"];
        $data=array_add($data,"type",$type);

        if(!$this->validate(["name"=>$request['name']])){
            return $this->showErrors();
        }else{
            $data=array_add($data,"name",$request['name']);
        }
        $parent_id=null;
        if(!is_null($request["key"])){
            $category_parent=$this->convertKey($request["key"]);
            if(!$category_parent){
                return false;
            }
            $parent_id=$category_parent->id;
            $data=array_add($data,"parent_id",$parent_id);
            data_set($data,"lang",$category_parent->lang);
            data_set($data,"type",$category_parent->type);
        }
        $this->create($data);
        return $data["key"];
    }
    public function updateAvatarDB($id,$media_files_id){
	    return $this->_model->where("id",$id)->update(['avatar'=>$media_files_id]);
    }

    //=====================method support=========================
    public function convertKey($key){
        return $this->_model->where("key",$key)->first();
    }
    private function getListIdParent($id){
        $data=[];
        $id=$this->_model->find($id)->parent()->value("id");
        while ($id!=null) {
            array_push($data,$id);
            $id=$this->_model->find($id)->parent()->value("id");
        }
        return array_sort($data);
    }
    public function validate($attributes)
    {
        $name=array_get($attributes,"name");
        $rules      =[
            'name'         =>'required|max:255|string'
        ];
        $validator   = Validator::make($attributes,$rules);
        if($validator->fails()){
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }
    public function keyCategory(){
        $key=str_random(config("category.length_key"));
        while($this->_model->where("key",$key)->exists()){
            $key=str_random(config("category.length_key"));
        }
        return $key;
    }
}