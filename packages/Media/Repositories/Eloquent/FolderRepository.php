<?php

namespace Packages\Media\Repositories\Eloquent;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Application;
use Illuminate\Database\Eloquent\Collection;
use League\Flysystem\Exception;
use Packages\Core\Repositories\BaseRepository;
use Carbon\Carbon;
use Validator;
use Auth;
use Packages\Media\Models\Folder;
use Packages\Media\Repositories\Contracts\FolderRepositoryInterface;
use Packages\Media\Repositories\Contracts\FileRepositoryInterface;

class FolderRepository extends BaseRepository implements FolderRepositoryInterface{
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

    protected $media;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->media = $this->app->make("media");
    }
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return Folder::class;
    }
    /**
     * get All folder user
     * @param  [int] $id [id folder]
     * @return [type]     [description]
     */
    public function getAll($folder){
        $t=[];
        $data=$this->_model->find($folder->id)
            ->children()
            ->where("add_by",Auth::user()->id)
            ->where("show",true)
            ->orderByRaw('updated_at DESC')
            ->select("id","name","share")->get();
        foreach ($data as $value) {
            $t[]=[
                "name"=>str_limit($value->name, 15, $end = '...'),
                "id"  =>$value->id,
                "share"=>$value->share
            ];
        }
        return $t;
        throw new Exception('Not found folder.');
    }
    public function getFolder($folder){
        $t=[];
        $data=$this->_model->find($folder->id)
                ->children()
                ->where("add_by",Auth::user()->id)
                ->where("deleted_at",null)
                ->where("show",true)
                ->orderByRaw('updated_at DESC')
                ->get();
        foreach ($data as $value) {
            $t[]=[
                "name"=>str_limit($value->name, 15, $end = '...'),
                "id"  =>$value->id,
                "share"=>$value->share
            ];
        }
        return $t;
        throw new Exception('Not found folder.');
    }
    public function getFolderTrash($folder){
        if($this->getIdUserRecycle()!=$folder->id){
            if(is_null($folder->deleted_at)){
                    abort(404,"Not found page");
            }
        }
        $t=[];
        $data=$this->_model->find($folder->id)
                ->children()
                ->where("add_by",Auth::user()->id)
                ->where("deleted_at","<>",null)
                ->where("show",true)
                ->orderByRaw('updated_at DESC')
                ->get();
        foreach ($data as $value) {
            $t[]=[
                "name"=>str_limit($value->name, 15, $end = '...'),
                "id"  =>$value->id,
                "share"=>$value->share
            ];
        }
        return $t;
        throw new Exception('Not found folder.');
    }
    public function makeFolder($name,$key=null){
        if($this->validate(["name"=>$name])){
            if($key==null){
                $parent_id=null;
            }else{
                $parent_id=$this->_model->where("share",$key)->value("id");
            }
            $this->makeFolderToDB($name,$parent_id);
        }
        return $this->showErrors();
    }

    /**
     * Bereadrum folders
     * @param $key
     * @return string
     */
    public function getBreadrumbFolder($folder){
        $data=[];
        $id=$folder->id;
        foreach ($this->getListIdParent($id) as $value) {
            $folder_db=$this->_model->where("id",$value)->first();
            array_push($data,[
                "share"=>$folder_db->share,
                "name"=>str_limit($folder_db->name, 15, $end = '...'),
            ]);
        }
        array_push($data,[
            "share"=>$folder->share,
            "name"=>str_limit($folder->name, 15, $end = '...'),
        ]);
        return $data;
    }
    /**
     * get id folder default user curent login
     * @return [int] [id folder]
     */
    public function getIdUserRoot(){
        return $this->_model->where('parent_id',null)->where("name","Root")
            ->where('add_by',Auth::user()->id)->value("id");
    }
    public function getUserRoot(){
          return $this->_model->where('parent_id',null)->where("name","Root")
            ->where('add_by',Auth::user()->id)->first();
    }
    /**
     * get id folder default user curent login
     * @return [int] [id folder]
     */
    public function getIdUserRecycle(){
        return $this->_model->where('parent_id',null)->where("name","Recycle")
            ->where('add_by',Auth::user()->id)->value("id");
    }
    /**
     * @param $id
     * @return mixed
     */
    public function convertIdToShare($id){
        return $this->_model->where('id',$id)->value('share');
    }

    public function convertShareToId($share,$t=false){
        $folder=$this->_model->where('share',$share)->first();
        if(is_null($folder)){
            return abort(404);
        }
        if($t){
            return $folder;
        }else{
            return $folder->id;
        }
    }

    public function makeFolderToDB($name,$parent_id=null){
        $data=[
            "name" 			=>$name,
            "parent_id"     =>$parent_id,
            "show"          =>1,
            "add_by"        =>Auth::user()->id,
            "share" 		=>$this->createdKeyShare(),
            "created_at"    =>Carbon::now(),
        ];
        if($name==="Root" || $name==="Recycle"){
            $data=array_add($data,"default",1);
            data_set($data,"show",0);
        }
        try {
            $this->create($data);
        }catch(Exception $e){
            report($e);
            return false;
        }

    }
    public function makeFolderToStorage($path){
        try{
            if(!($this->checkFolderExist($path) && $path))
            {
                Storage::makeDirectory($path);
                return $path;
            }
            return false;
        }catch(Exception $e){
            report($e);
            return false;
        }
    }
    /**
     * Check exist folder 
     * @param  [type] $path [description]
     * @return [type]       [description]
     */
    public function checkFolderExist($path){
        if(Storage::exists($path)){
            return true;
        }else{
            return false;
        }
    }
    public function getPath(){
        $path=str_random(config("media.length_name"));
        if(Storage::exists($path)|| ($this->_model->where("path",$path)->count()>0)){
            $path=str_random(config("media.length_name"));
        }
        return $path;
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
    public function createdKeyShare(){
        $share=str_random(config("media.length_share"));
        while($this->findShareExists($share)){
            $share=str_random(config("media.length_share"));
        }
        return $share;
    }

    /**
     *
     * @param $share
     * @return mixed
     */
    public function findShareExists($share){
        return $this->_model->where("share",$share)->exists();
    }

    /**
     * get list parent id;
     * @param $id
     * @return array
     */
    public function getListIdParent($id){
        $data=[];
        $id=$this->_model->find($id)->parent()->value("id");

        while ($id!=null) {
            array_push($data,$id);
            $id=$this->_model->find($id)->parent()->value("id");
        }
        return array_sort($data);
    }


    /** trash folders
     * @param array $folders
     * @return bool
     */
    public function trashFolders($folders=[]){
        if(is_array($folders)){
            foreach ($folders as $key) {
                $id=$this->convertShareToId($key);
                $this->transhFolder($id)->transhFolderParrent($id);
                foreach(explode(",",$this->getAllChildren($id)) as $item){
                    $this->transhFolder($item);
                }
            }
            return $this;
        }
    }
    /**
     * Transh folder daleted_at
     * @param $id
     */
    public function transhFolder($id){
        $this->_model->where("id",$id)->update(["deleted_at"=>Carbon::now()]);
        return $this;
    }
    /**
     * Transh folder parent
     * @param $id
     */
    public function transhFolderParrent($id){
        $this->_model->where("id",$id)->update(["parent_id"=>$this->getIdUserRecycle()]);
        return $this;
    }
    /**
     * Get all child
     * @param  [type] $folders   [description]
     * @param  [type] $parent_id [description]
     * @param  string $data      [description]
     * @return [type]            [description]
     */
    protected function getAllChildren($id,$string=""){
        $folders=$this->_model->select("id")->where("parent_id",$id)->get();
        $data=[];
        foreach ($folders as $folder) {
            $string=$this->getAllChildren($folder->id,$string.",".$folder->id);
        }
        return trim($string,",");
    }

    public function checkFolderDefault(){
        if(!$this->getIdUserRoot()){
            $this->makeFolder("Root");
        }
         if(!$this->getIdUserRecycle()){
            $this->makeFolder("Recycle");
        }
        return $this;
    }
    
    /**
     * get list parent id recycle;
     * @param $id
     * @return array
     */
    public function getListIdParentRecycle($id){
        $data=[];
        $id=$this->_model->find($id)->parent()->value("id");
        while ($id!=null) {
            array_push($data,$id);
            $id=$this->_model->find($id)->parent()->value("id");
        }
        return array_sort($data);
    }
    /**
     * restore
     * @param  [type] $parent_id [description]
     * @param  array  $ids       [description]
     * @return [type]            [description]
     */
    public function restore($share,$parent_share=null){
        $parent_id=$this->getIdUserRoot();
        if(is_array($share)){
            foreach ($share as $s) {
                $id= $this->convertShareToId($s);
                $this->restoreParentId($id, $parent_id)->restoreDeleted_at($id);
                foreach(explode(",",$this->getAllChildren($id)) as $item){
                    $this->restoreDeleted_at($item);
                }
            }
        }
        return $this->convertIdToShare($parent_id);
    }
    /**
     * [restoreParentId description]
     * @param  [type] $id        [description]
     * @param  [type] $parent_id [description]
     * @return [type]            [description]
     */
    public function restoreParentId($id,$parent_id){
        $this->_model->where("id",$id)->update(["parent_id"=>$parent_id]);
        return $this;
    }
    /**
     * [restoreDeletedat description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function restoreDeleted_at($id){
         $this->_model->where("id",$id)->update(["deleted_at"=>NULL]);
         return $this;
    }
    /**
     * delete folder
     * @param  [type] $share [description]
     * @return [type]        [description]
     */
    public function delete($share,$mediaRepositoryFile){

        if(is_array($share)){
            $data="";
            $ids=[];
            foreach ($share as $s) {
                $id= $this->convertShareToId($s);
                array_push($ids,$id);
                $data=$data.",".$id.",".trim($this->getAllChildren($id),",");
            }
            foreach($ids as $id){
                $mediaRepositoryFile->deteleToFolderID($id);
                $this->_model->find($id)->delete();
            }
        }
        return true;
    }

    public function move($share,$dShare){
        $id=$this->convertShareToId($dShare);
        if(is_array($share)){
            foreach ($share as $k){
                $ids=$this->convertShareToId($k);
                if($ids) {
                    $this->_model
                        ->where("id", $ids)
                        ->where("add_by", Auth::user()->id)
                        ->update(["parent_id" => $id]);
                    return true;
                }
            }
        }
    }
    public function showErrors(){
        if(!is_null($this->errors)){
            return response()->json([
                "message"=>($this->errors)
            ],442);
        }
    }
    /**
     * Share folder
     * @param  [type]  $key  [description]
     * @param  integer $page [description]
     * @return [type]        [description]
     */
    public function search($key,$rcy,$r){
        if(is_null($rcy)){
             $folder= $this->_model->where("add_by",Auth::user()->id)
                 ->where('name', 'LIKE', '%'.$key.'%')
                 ->where('show','<>','null')
                 ->where('deleted_at',null)

                ->paginate(config("media.paginate"))->appends(request()->query());
        }else{
            $folder= $this->_model->where("add_by",Auth::user()->id)
                ->where([
                    ['show','<>','null'],
                   ['deleted_at','<>',null],
                ])
                ->where('name', 'LIKE', '%'.$key.'%')
                ->paginate(config("media.paginate"))->appends(request()->query());

        }
        $folder->getCollection()->transform(function($value){
            $path="/";
            $total=$value->total;
            foreach ($this->getListIdParent($value->id)as $id) {
                $path=trim($path."/".$this->_model->where("id",$id)->value("name"),"/");
            };
            $path=str_replace("Root",".",$path)."/".$value->name;
            return[
                "id"=>$value->id,
                "name"=>str_limit($value->name, 15, $end = '...'),
                "share"=>$value->share,
            ];
       });
        if(!is_null($r)){
            $folder->put(0,["id"=>$this->getUserRoot()->id,"name"=>$this->getUserRoot()->name,"share"=>$this->getUserRoot()->share]);
        }
        return $folder;
    }
}





