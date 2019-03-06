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

class FolderRepositoryV1 extends BaseRepository implements FolderRepositoryInterface{
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
    public function getFolders($option,$parent_id=null){
    	switch ($option) {
    		case 'del':
    			return $this->getDataDelete($parent_id);
    			break;
    		case 'all':
    			return $this->getAllData();
    			break;
    		default:
    			return $this->getDataToUser($this->getUserIdCurent(),$parent_id);
    			break;
    	}
    }
    private function getDataDelete($parent_id){
    	return $this->_model
    				->where("deleted_at","<>",null)
    				->where("show",true)
    				->where("parent_id",$parent_id)
                    ->orderByRaw('created_at', 'desc')
                    ->orderByRaw('updated_at', 'desc')
    				->get();
    }
    private function getAllData(){
    	return $this->_model>where("show",true)->orderBy('updated_at', 'desc')->get();
    }
    private function getDataToUser($user_id,$parent_id){
    	return $this->_model
                    ->where("deleted_at",null)
    				->where("add_by",$user_id)
    				->where("parent_id",$parent_id)
    				->where("show",true)
                    ->orderByRaw('created_at', 'desc')
                    ->orderByRaw('updated_at', 'desc')
    				->get();
    }
    /**
     * Add new folder
     * @param  [type] $name [description]
     * @param  [type] $key  [description]
     * @return [type]       [description]
     */
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
    private function makeFolderToDB($name,$parent_id){
        $data=[
            "name" 			=>$name,
            "parent_id"     =>$parent_id,
            "show"          =>1,
            "add_by"        =>$this->getUserIdCurent(),
            "share" 		=>$this->createdKeyShare(),
            "created_at"    =>Carbon::now(),
        ];
        try {
            $this->create($data);
        }catch(Exception $e){
            report($e);
            return false;
        }

    }

    /**
     * Bereadrum folders
     * @param $key
     * @return string
     */
    public function getBreadrumbFolder($folder,$type){
         $data[]=[
                    "name"=>Auth::user()->name,
                    "share"=>""
                ];
        if($type=="recycle"){
            $data[]=[
                    "name"=>"Recyle bin",
                    "share"=>"recycle"
                ];
        }
        if($folder){
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
        }
        return $data;
    }
    /**
     * get list parent id;
     * @param $id
     * @return array
     */
    private function getListIdParent($id){
        $data=[];
        $id=$this->_model->find($id)->parent()->value("id");

        while ($id!=null) {
            array_push($data,$id);
            $id=$this->_model->find($id)->parent()->value("id");
        }
        return array_sort($data);
    }
    /**
     * move folders
     * @param  [type] $share  [description]
     * @param  [type] $dShare [description]
     * @return [type]         [description]
     */
    public function move($share,$dShare){
        try {
            if (is_null($this->convertFormShare($dShare))) {
                $id = null;
            } else {
                $id = $this->convertFormShare($dShare)->id;
            }
            if (is_array($share)) {
                foreach ($share as $k) {
                    $ids = $this->convertFormShare($k)->id;
                    $this->_model
                        ->where("id", $ids)
                        ->where("add_by", $this->getUserIdCurent())
                        ->update(["parent_id" => $id]);
                }
            }
        }catch(Exception $e){
            report($e);
        }
    }
    /**
     * Share folder
     * @param  [type]  $key  [description]
     * @param  integer $page [description]
     * @return [type]        [description]
     */
    public function search($key,$rcy,$folders){
        if(is_null($rcy)){
            $folder = $this->searchFolder($key);
        }else{
            $folder =  $this->searchFolderDelete($key);
        }
        if(!is_null($folders)){
            $folder = $this->searchFolderMove($key,explode(",",$folders));

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

        $custom = collect(['folderRoot' => ["name" => Auth::user()->name, "share" => ""]]);
        $folder = $custom->merge($folder);

        return $folder;
    }
    public function searchFolderMove($search,$foldersChoice){
        if(is_array($foldersChoice)){
            $where=[];
            foreach ($foldersChoice as $item){
                $id=$this->convertFormShare($item)->id;
                $where[]=$id;
                $where=array_merge($where,explode(",",$this->getAllChildren($id)));
            }
            //dd($where);
            return $this->_model
                ->whereNotIn("id",$where)
                ->where("deleted_at",null)
                ->where('show',true)
                ->where('name', 'LIKE', '%'.$search.'%')
                ->where("add_by",$this->getUserIdCurent())
                ->paginate(config("media.paginate"))->appends(request()->query());
        }
    }
    private function searchFolder($search){
        return $this->_model
            ->where("deleted_at",null)
            ->where('show',true)
            ->where('name', 'LIKE', '%'.$search.'%')
            ->where("add_by",$this->getUserIdCurent())
            ->paginate(config("media.paginate"))->appends(request()->query());
    }
    private function searchFolderDelete($search){
        return $this->_model
            ->where("deleted_at","<>",null)
            ->where('show',true)
            ->where('name', 'LIKE', '%'.$search.'%')
            ->where("add_by",$this->getUserIdCurent())
            ->paginate(config("media.paginate"))->appends(request()->query());
    }
    /** trash folders
     * @param array $folders
     * @return bool
     */
    public function trashFolders($folders=[],$mediaRepositoryFile){
        if(is_array($folders)){
            foreach ($folders as $key) {
                $id=$this->convertFormShare($key)->id;
                $this->transhFolder($id,true);
                $mediaRepositoryFile->trashFilesToFolder($id);
                foreach(explode(",",$this->getAllChildren($id)) as $item){
                    $mediaRepositoryFile->trashFilesToFolder($item);
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
    public function transhFolder($id,$parent=false){
        $data=[
            "deleted_at"=>Carbon::now()
        ];
        if($parent){
            $data=array_add($data,"parent_id",null);
        }
        $this->_model->where("id",$id)->update($data);
        return $this;
    }
    /**
     * Get all children
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
    /**
     * restore
     * @param  [type] $parent_id [description]
     * @param  array  $ids       [description]
     * @return [type]            [description]
     */
    public function restore($share){
        try {
            if (is_array($share)) {
                foreach ($share as $s) {
                    $id = $this->convertFormShare($s)->id;
                    $this->restoreItem($id);
                    foreach (explode(",", $this->getAllChildren($id)) as $item) {
                        $this->restoreItem($item);
                    }
                }
            }
        }catch(Exception $e){
            report($e);
        }
    }
    public function restoreItem($id){
        return $this->_model->where("id",$id)->update(["deleted_at"=>NULL]);
    }

    public function delete($folders,$mediaRepositoryFile){
        try {
            if (is_array($folders)) {
                $data = "";
                $ids = [];
                foreach ($folders as $s) {
                    $id = $this->convertFormShare($s)->id;
                    $data = $data . "," . $id . "," . trim($this->getAllChildren($id), ",");
                }
                $ids=explode(",",trim($data,","));
                foreach ($ids as $item) {
                    $mediaRepositoryFile->deteleToFolderID($item);
                }
                foreach ($ids as $item){
                    $this->_model->where("id",$item)->delete();
                }
                return true;
            }
        }catch(Exception $e){
            report($e);
        }
    }
    /**
     * =======================------------====================================----------
     * =======================------------====================================----------
     * Vadialate data
     * @param  [type] $attributes [description]
     * @return [type]             [description]
     */
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
    /**
     * check errors
     * @return [type] [description]
     */
    private function showErrors(){
        if(!is_null($this->errors)){
            return response()->json([
                "message"=>($this->errors)
            ],442);
        }
        return $this;
    }
    /**
     *  Get User id action
     */
    private function getUserIdCurent(){
    	return Auth::user()->id;
    }
    /**
     * make key share
     * @return [type] [description]
     */
    private function createdKeyShare(){
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
    private function findShareExists($share){
        return $this->_model->where("share",$share)->exists();
    }

    public function convertFormShare($share){
    	return $this->_model->where("share",$share)->first();
    }
}






