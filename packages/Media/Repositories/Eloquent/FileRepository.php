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
use File;
use Image;
use Packages\Media\Models\Folder;
use Packages\Media\Models\FileUpload;
use Packages\Media\Repositories\Contracts\FileRepositoryInterface;

class FileRepository extends BaseRepository implements FileRepositoryInterface{
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

    protected $folder_id;
	public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->media = $this->app->make("media");
    }
    public function getModel()
    {
        return FileUpload::class;
    }
    public function getAll($option,$folder){
	    if($folder){
            $folder_id=$folder->id;
        }else{
	        $folder_id=null;
        }
        switch ($option) {
            case 'del':
                $data= $this->getDataDelete($folder_id);
                break;
            default:
                $data=$this->_model->where("folders_id",$folder_id)
                        ->where("deleted_at",null)
                        ->where("add_by",Auth::user()->id)
                        ->where("show",true)
                        ->orderByRaw('created_at DESC')
                        ->orderByRaw('updated_at DESC')
                        ->paginate(config("media.paginate_file"))->appends(request()->query());
                break;
        }
        $data->getCollection()->transform(function($value){
            $name=$this->crop_name_file($value->name,config("media.length_name_file_crop"));
            if(!is_null($value->size)){
                $name="(".$value->size.")".$name;
            }
            return [
                "id"=>$value->id,
                "name"=>$name,
                "share"=>$value->share,
                "path"=>config("filesystems.disks.public.url")."/thumbnails/".$value->thumbnail,
                "link"=>"/storage/".$value->path,
                "type"=>$this->showImageOrFile($value->type),
            ];
        });
        return $data;
    }
    private function getDataDelete($folder_id){
        return $this->_model->where("folders_id",$folder_id)
            ->where("deleted_at","<>",null)
            ->where("add_by",Auth::user()->id)
            ->where("show",true)
            ->orderByRaw('created_at DESC')
            ->orderByRaw('updated_at DESC')
            ->paginate(config("media.paginate_file"))->appends(request()->query());
    }
    public function uploadFiles($files,$folder){
        if(!$folder){
            $this->folder_id=null;
        }else{
            $this->folder_id=$folder->id;
        }
        $path="";
        if($files){
            foreach($files as $file) {
                $type=File::extension($file->getClientOriginalName());
                $name_file=$file->getClientOriginalName();

                if($this->checkFileUpload($file)){
                    $url = Storage::putFile($path, $file,Auth::user()->id);//path file uplod
                    //save data base
                    if($this->validate(["name"=>$name_file])){
                       $data=[
                           "name"           => $name_file,
                           "type"           =>$type,
                           "path"           =>$url,
                           "show"           =>true,
                           "folders_id"      =>$this->folder_id,
                           "add_by"         =>Auth::user()->id,
                           "share"          =>$this->createdKeyShare(),
                           "created_at"     =>Carbon::now(),
                       ];
                       $id=$this->create($data);
                    }
                    //created thumbnail images default
                    if(array_key_exists($type,config("media.type_images"))){
                        $url=$this->resize_image($file,$id,$path,config("media.thumb_img_width"),config("media.thumb_img_height"));
                    }
                }
            }
        }
        if(!is_null($this->errors)){
            return $this->showErrors($this->errors);
        }
        return "ok";
    }
    public function uploadImage($files,$name=null){
            if(!$this->checkImageBase64($files)){
                return false;
            }
            $r=str_random(28);
            if($name==null){
                $name=$r;
            }
            $name=$this->crop_name_file($name,config("media.length_name_file_crop"));
            $path=config("filesystems.disks.public.root")."/".$r.".png";
            try{
                Image::make(file_get_contents($files))->save($path);
            }catch (\Exception $e) {
                return $e->getMessage();
            }
            $data=[
                    "name"           => $name,
                    "type"           =>"png",
                    "path"           =>$r.".png",
                    "show"           =>false,
                    "folders_id"     =>null,
                    "add_by"         =>Auth::user()->id,
                    "share"          =>$this->createdKeyShare(),
                    "created_at"     =>Carbon::now(),
                ];
            $id=$this->_model->insertGetId($data);
            if($id){
                $url=$this->resize_image($files,$id,"",config("media.thumb_img_width"),config("media.thumb_img_height"));
                return $id;
            }
    }
    public function updateUploadImage($files,$name,$id){
        if(!$this->checkImageBase64($files)){
            return false;
        }
        $image=$this->_model->where("id",$id)->first();
        if($this->deleteImage($image->path,$image->thumbnail)){
            $r=str_random(28);
            $path=config("filesystems.disks.public.root")."/".$r.".png";
            try{
                Image::make(file_get_contents($files))->save($path);
            }catch (\Exception $e) {
                return $e->getMessage();
            }
            $this->_model->where("id",$id)->update(['name'=>$name,'path'=>$r.".png"]);
            $url=$this->resize_image($files,$id,"",config("media.thumb_img_width"),config("media.thumb_img_height"));
            return $id;
        }
        return $id;
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

    public function showErrors($errors){
            return response()->json([
                "message"=>$errors
            ],442);
    }
    public function resize_image($file,$id,$path,$width,$height){
        $width_image=getimagesize($file)[0];
        $height_image=getimagesize($file)[1];
        $path=$path."/thumbnails";
        $ext=$this->getNameFile($id,false);
        $name_thumbnail=$this->getNameFile($id)."_".$width."x".$height.".".$ext;
        $image = Image::make($file);
        $image->resize($width,$height);
        Storage::makeDirectory($path);
        $image->save(config("filesystems.disks.public.root").$path."/".$name_thumbnail);
        $data= [
            "thumbnail"    =>$name_thumbnail,
            "size"         =>$width_image."x".$height_image,
        ];
        $this->_model->where("id",$id)->update($data);
        return $path."/".$name_thumbnail;
    }
    public function getNameFile($id,$t=true){
        $path=$this->_model->where("id",$id)->value("path");
        if($t){
            return array_first(explode(".",$path));
        }
        return array_last(explode(".",$path));
    }
    public function checkFileUpload($file){
        $type=File::extension($file->getClientOriginalName());
        $size=round($file->getClientSize()/1024);
        if(!array_key_exists($type,config("media.file_type_array"))){
           $this->errors[]=trans("media.upload_nofication.accept",['name'=>$file->getClientOriginalName()]);
           return false;
        }
        if(array_key_exists($type,config("media.type_images"))){
            if($size>config("media.size_upload_image")){
                $this->errors[]=trans("media.upload_nofication.image_big",['name'=>$file->getClientOriginalName().'-'.$size.'Kb']);
                return false;
            }
        }else{
            if($size>config("media.max_size_upload")){
                 $this->errors[]=trans("media.upload_nofication.image_big",['name'=>$file->getClientOriginalName()]);
                 return false;
            }
        }
        return true;
    }
    public function crop_name_file($name,$number){
        return str_limit($name, $number, $end = '....'.array_last(explode(".",$name)));
    }
    public function showImageOrFile($type){
        if(array_key_exists($type,config("media.type_images"))){
            return "image";
        }else{
            $type_show=array_get(config("media.file_icon_array"),$type);
            if(!$type_show){
                 $this->errors[]=Exception('File now allow show');
            }
            return $type_show;
        }
    }
    public function trashFiles($files=[]){
        if(is_array($files)){
            foreach ($files as $file) {
                $this->transhFile($this->convertShareToId($file));
            }
        }
        return $this;
     }
    /**
     * Transh folder daleted_at
     * @param $id
     */
    public function transhFile($id,$folder_id=null){
        if(!is_null($folder_id)){
            $this->_model->where("id",$id)->update(["deleted_at"=>Carbon::now(),"folders_id"=>$folder_id]);
        }else{
            $this->_model->where("id",$id)->update(["deleted_at"=>Carbon::now(),"folders_id"=>null]);
        }
        return $this;
    }
    public function trashFilesToFolder($folder_id){
         $this->_model->where("folders_id",$folder_id)->update(["deleted_at"=>Carbon::now()]);
         return $this;
    }
    /**
     * @param $id
     * @return mixed
     */
    public function convertShareToId($share){
        return $this->_model->where('share',$share)->value('id');
    }
    public function delete($files){
        if(is_array($files)){
            foreach ($files as $file) {
                $data=$this->_model->where("share",$file)->where("add_by",Auth::user()->id);
                if(Storage::exists($data->value("path"))){
                    Storage::delete($data->value("path"));
                }
                if(!is_null($data->value("thumbnail"))){
                    if(Storage::exists("thumbnails/".$data->value("thumbnail"))){
                        Storage::delete("thumbnails/".$data->value("thumbnail"));
                    }
                }
                $this->_model->find($data->value("id"))->delete();
            }
        }
        return $this;
    }
    public function deteleToFolderID($folder_id){
        $data=$this->_model->where("folders_id",$folder_id)->where("add_by",Auth::user()->id);
        if($data->count()>0){
            foreach ($data->get() as $value) {
               // print_r($value->path);
                if(Storage::exists($value->path)){
                    Storage::delete($value->path);
                }
                if(!is_null($value->thumbnail)){
                    if(Storage::exists("thumbnails/".$value->thumbnail)){
                        Storage::delete("thumbnails/".$value->thumbnail);
                    }
                }
            }
        }
        $this->_model->where("folders_id",$folder_id)->where("add_by",Auth::user()->id)->delete();
    }
    public function search($key,$cry){
        if($cry){
             $files= $this->_model->where("add_by",Auth::user()->id)
                    ->where("add_by",Auth::user()->id)
                    ->where("show",true)
                    ->where("deleted_at","<>",null)
                    ->where("name","like","%{$key}%")
                    ->paginate(config("media.paginate_file"))->appends(request()->query());

            }else{
            $files= $this->_model->where("add_by",Auth::user()->id)
                ->where("add_by",Auth::user()->id)
                ->where("show",true)
                ->where("deleted_at",null)
                ->where("name","like","%{$key}%")
                ->paginate(config("media.paginate_file"))->appends(request()->query());
        }
        $files->getCollection()->transform(function($value){
            return [
                "id"=>$value->id,
                "name"=>$this->crop_name_file($value->name,config("media.length_name_file_crop")),
                "share"=>$value->share,
                "path"=>config("filesystems.disks.public.url")."/".$value->path,
                "type"=>$this->showImageOrFile($value->type),
            ];
        });
        return $files;
    }
    public function move($files,$folder_id){
        try {
            if (is_array($files)) {
                foreach ($files as $item) {
                    $id = $this->convertShareToId($item);
                    if ($id) {
                        $this->_model
                            ->where("id", $id)
                            ->where("add_by", Auth::user()->id)
                            ->update(["folders_id" => $folder_id]);
                    }
                }
                return true;
            }
        }catch(Exception $e){
            report($e);
        }
    }

    public function getPathFile($id){
        $path=$this->_model->where("id",$id)->value("path");
        return config("filesystems.disks.public.url")."/".$path;
    }
    public function checkImageBase64($files){
        if(is_string($files)) {
            $string_a = explode(";", $files);
            if($string_a[0]!=="data:image/png"){
                return false;
            }
            return true;
        }
        return false;
    }
    public function deleteImage($path,$thumbnail){
        try{
            if(Storage::exists($path)){
                Storage::delete($path);
            }
            if(Storage::exists("thumbnails/".$thumbnail)){
                Storage::delete("thumbnails/".$thumbnail);
            }
            return true;
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}