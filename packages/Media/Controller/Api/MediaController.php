<?php
namespace Packages\Media\Controller\Api;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

use Packages\Core\Controllers\Controller;
use Packages\Media\Repositories\Contracts\FolderRepositoryInterface;
use Packages\Media\Repositories\Contracts\FileRepositoryInterface;
use Packages\Post\Repositories\Contracts\PostRepositoryInterface;
use Packages\Media\Models\RouteFiles;

class MediaController extends Controller
{
    protected $mediaRepositoryFolder;
    protected $mediaRepositoryFile;
    protected $postRepository;

    public function  __construct(FolderRepositoryInterface $mediaRepositoryFolder,
                                 FileRepositoryInterface $mediaRepositoryFile,
                                 PostRepositoryInterface $postRepository
    ){
        $this->mediaRepositoryFolder        = $mediaRepositoryFolder;
        $this->mediaRepositoryFile          = $mediaRepositoryFile;
        $this->postRepository        	    = $postRepository;
    }
    public function getAll(Request $request){
        /*
            Get folder
         */
        $option=null;
        $parent_id=null;
        $type="";
        $get_folder=null;
        if(!is_null($request["share"])){
            switch ($request["share"]){
                case "recycle":
                    $type="recycle";
                    $option="del";
                    break;
                default:
                    $get_folder=$this->mediaRepositoryFolder->convertFormShare($request["share"]);
                    if($get_folder) {
                        $parent_id=$get_folder->id;
                        if (!is_null($get_folder->deleted_at)) {
                            $type = "recycle";
                            $option = "del";
                        }
                    }
                    break;
            }
        }
        /*========================================================================
            get Foldes
         */
        $folders=[];
        foreach ($this->mediaRepositoryFolder->getFolders($option,$parent_id) as $value) {
            $folders[]=[
                "name"=>str_limit($value->name, 15, $end = '...'),
                "id"  =>$value->id,
                "share"=>$value->share
            ];
        }
        /*========================================================================
            get Filess
         */
        $files=$this->mediaRepositoryFile->getAll($option,$get_folder);

        /* ========================================================================
            get breadcrumb
         */
        $breadcrumb=$this->mediaRepositoryFolder->getBreadrumbFolder($get_folder,$type);
        return response()->json([
            'folders'       => $folders,
            'files'         => $files,
            'breadcrumb'    => $breadcrumb,
            'type'         =>  $type,
        ]);
    }
    public function createFolder(Request $request){
        $key                    = $request["share"];
        $name                   = $request["name"];
        try {
            $this->mediaRepositoryFolder->makeFolder($name,$key);
        }catch(Exception $e){
            report($e);
            return false;
        }
    }
    /**
     * search
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function search(Request $request){
        $data           =[];
        $value          =$request["value"];
        $rcy            =$request["rcy"];
        $folders         =$request["folders"];

        $data=array_add($data,"folders",$this->mediaRepositoryFolder->search($value,$rcy,$folders));
        if(is_null($request["of"])){
            $data=array_add($data,"files",$this->mediaRepositoryFile->search($value,$rcy));
        }
        return response()->json($data);
    }
    /**
     * move folder
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function move(Request $request){
        $shareFolders                 = $request["shareFolders"];
        $shareFiles                   = $request["shareFiles"];
        $dshareFolder                 = $request["dshareFolder"];
        $this->mediaRepositoryFolder->move($shareFolders,$dshareFolder);
        $folder_id=$this->mediaRepositoryFolder->convertFormShare($dshareFolder);
        if($folder_id){
            $folder_id=$folder_id->id;
        }else{
            $folder_id=null;
        }
        $this->mediaRepositoryFile->move($shareFiles,$folder_id);
        echo $dshareFolder;
    }
    public function recycle(Request $request){
       $folders= $request["folders"];
       $files= $request["files"];
       $this->mediaRepositoryFolder->trashFolders($folders,$this->mediaRepositoryFile);
       $this->mediaRepositoryFile->trashFiles($files);
    }
    public function restore(Request $request){
        $folders                 = $request["folders"];
        return response()->json([
            'data'=>$this->mediaRepositoryFolder->restore($folders)
        ]);
    }
    public function delete(Request $request){
        $folders                 = $request["folders"];
        $files                   = $request["files"];
        $this->mediaRepositoryFolder->delete($folders,$this->mediaRepositoryFile);
        $this->mediaRepositoryFile->delete($files);
    }
    public function uploadFile(Request $request){
        $files                        =   $request["input_name"];
        $share                        =   $request["folder_upload"];
        $folder                 =   $this->mediaRepositoryFolder->convertFormShare($share);
        return $this->mediaRepositoryFile->uploadFiles($files,$folder);
    }
}


