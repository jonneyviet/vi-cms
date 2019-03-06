<?php

namespace Packages\Post\Repositories\Eloquent;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Application;
use Illuminate\Database\Eloquent\Collection;
use League\Flysystem\Exception;
use Packages\Core\Repositories\BaseRepository;
use Carbon\Carbon;
use Validator;
use Auth;
use Packages\Post\Models\Post;
use Packages\Post\Repositories\Contracts\PostRepositoryInterface;


class PostRepository extends BaseRepository implements PostRepositoryInterface{
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
        //$this->_model=$this->_model->where('lang','vi'); setting languages
    }
    public function getModel()
    {
        return Post::class;
    }
    public  function getAll($option=null){
        switch ($option) {
            case 'all':
                $data=$this->_model
                    ->orderByRaw('created_at DESC')
                    ->orderByRaw('updated_at DESC')->select('name');
                break;
            case 'del':
                $data=$this->_model
                    ->where("is_public",true)
                    ->where("deleted_at","<>",null)
                    ->orderByRaw('created_at DESC')
                    ->orderByRaw('updated_at DESC');
                break;
            default:
                $data=$this->_model
                    ->orderByRaw('created_at DESC')
                    ->orderByRaw('updated_at DESC')
                    ->paginate(config("media.paginate"))->appends(request()->query());
                break;
        }
        $data->getCollection()->transform(function($value){
            return [
                "id"=>$value->id,
                "lang"=>$value->lang,
                "name"=>str_limit($value->name,config("category.length_name_crop"),"..."),
                "key"=>$value->key,
                "is_public"=>$value->is_public,
                "date"=>(($value->updated_at==null)?$value->created_at->toDateTimeString():$value->updated_at->toDateTimeString()),
            ];
        });
        return $data;
    }
    public function getType($key,$option){
	    $category=$this->convertKey($key);
	    if(!$category){
            return abort(404);
        }else{
            $id=$category->id;
        }
        $option_array=explode(',',$option);
	    $data=[];
	    if(is_array($option_array)){
            foreach ($option_array as $item) {
                switch (trim($item)) {
                    case 'text':
                        $data=array_add($data,"text",($category->text==null)?$this->getTextJson($id):json_decode($category->text));
                        //$data=array_add($data,"text",json_decode($this->_model->where('id',$id)->value('text')));
                        break;
                    case 'name':
                        $data=array_add($data,"name",$this->_model->where('id',$id)->value('name'));
                        break;
                    case 'category':
                        $data_category=$this->_model->find($id);
                        $data=array_add($data,"category",["id"=>$data_category->category->id,"name"=>$data_category->category->name]);
                        break;
                    case 'date_time':
                        $t=$this->_model->where('id',$id)->value('created_at');
                        $data=array_add($data,"date_time",Carbon::instance($t)->toIso8601String());
                        break;
                    case 'avatar':
                        $a=$this->_model->find($id)->avatar()->value('path');
                        if($a){
                            $a=config("filesystems.disks.public.url").'/'.$a;
                        }else{
                            $a=null;
                        }
                        $data=array_add($data,"avatar",$a);
                        break;
                    default:
                        break;
                }
	        }
        }

        return response()->json([
            "data"=>$data
        ]);
    }
    public function createPost($name,$category){
	    
        if($this->validate(["name"=>$name])){
            $data=[
                "key"          =>$this->keyPost(),
                "name"          =>$name,
                "lang"          =>$category->lang,
                "category_id"     =>$category->id,
                "is_public"     =>false,
                "add_by"        =>Auth::user()->id,
                "created_at"    =>Carbon::now(),
            ];
           $this->create($data);
           return $data['key'];
        }else{
            return $this->showErrors();
        }

    }
    public function search($keyword,$option=null){

        switch ($option) {
            default:
                $data=$this->_model
                    ->where("is_public",true)
                    ->where("name","like","%{$keyword}%")
                    ->orderByRaw('created_at DESC')
                    ->orderByRaw('updated_at DESC');
                break;
        }
        return $data->paginate(config("media.paginate"))->appends(request()->query());
    }
    public function breadrumb($category){
         $data[]=[
                    "name"=>"",
                    "key"=>""
                ];
        if($category){
            $id=$category->id;
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
        }
        return $data;
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

    public function update($data){
	    $t=[];
	    if(is_array($data)){
            if(array_has($data,'key')){
                $category=$this->convertKey($data['key']);
                if($category){
                    $id =$category->id;
                    if(array_has($data,'name')){
                        if($this->_model->where("id",$id)->update(['name'=>$data['name']])){
                           $t=array_add($t,"name",$data['name']);
                        }
                    }
                    if(array_has($data,'text')){
                        if($this->_model->where("id",$id)->update(['text'=>json_encode($data['text'])])){
                            $t=array_add($t,"text",$data['text']);
                        }
                    }
                    if(array_has($data,'category_id')){
                        $this->_model->where("id",$id)->update(['category_id'=>$data['category_id']]);
                        if($this->_model->where("id",$id)->update(['category_id'=>$data['category_id']])){
                            $t=array_add($t,"category_id",$data['category_id']);
                        }
                    }
                    if(array_has($data,'created_at')){
                        if($this->_model->where("id",$id)->update(['created_at'=>$data['created_at']])){
                            $t=array_add($t,"text",$data['text']);
                        }
                    }
                    return $t;
                }
            }

        }
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
    public function keyPost(){
        $key=str_random(config("post.length_key"));
        while($this->_model->where("key",$key)->exists()){
            $key=str_random(config("post.length_key"));
        }
        return $key;
    }
    public function convertKey($key){
        return $this->_model->where("key",$key)->first();
    }
    private function showErrors(){
        if(!is_null($this->errors)){
            return response()->json([
                "message"=>($this->errors)
            ],442);
        }
        return $this;
    }
    private function getTypePost($id){
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
}