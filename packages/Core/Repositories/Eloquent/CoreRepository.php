<?php
namespace Packages\Core\Repositories\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Packages\Core\Models\Users;
use Auth;
use Packages\Core\Repositories\Contracts\CoreRepositoryInterface;
use packages\Media\Repositories\Eloquent\BaseRepository as MediaBaseRepository;


class CoreRepository implements CoreRepositoryInterface{
    protected $user;
    public function __construct(Users $user){
        $this->user=$user;
    }
    public function infoApp(){
        $data=[
            "menuLeft"=>$this->getMenuLeft()
        ];
        return $data;
    }
    /*
     * type Array;
     * return array[
     *  "name"=>string,
     *  "url"=>string,
     *  "icon"=>string,
     * ]
     *
     */
    public function getMenuLeft(){
        return $this->menuMedia();
    }
    public function menuMedia(){
        $folder=Users::find(Auth::user()->id)->folderUser->where("parent_id",null)->where("name","Root");
        $folder_recycle=Users::find(Auth::user()->id)->folderUser->where("parent_id",null)->where("name","Recycle");
        foreach ($folder as $value) {
            $path_folder=$value->share;
        }
        foreach ($folder_recycle as $value) {
            $path_folder_recycle=$value->share;
        }
        return[
            [
            "name"=>config("media.name"),
            "url"=>config("media.path").'/'.$path_folder,
            "icon"=>"glyphicon-hdd",
            ],
            [
            "name"=>config("media.name_recycle"),
            "url"=>config("media.path").'/rcy/'.$path_folder_recycle,
            "icon"=>"glyphicon-trash
",
            ],
        ];
    }
}