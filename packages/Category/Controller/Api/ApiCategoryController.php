<?php
namespace Packages\Category\Controller\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

use Packages\Category\Repositories\Contracts\CategoryRepositoryInterface;
use Packages\Media\Repositories\Contracts\FileRepositoryInterface;

class ApiCategoryController extends Controller
{
	protected $categoryRepository;
	protected $seoRepository;
	protected $option=0;

	public function __construct(CategoryRepositoryInterface $categoryRepository,FileRepositoryInterface $mediaRepositoryFile){
		 $this->categoryRepository        	= $categoryRepository;
        $this->mediaRepositoryFile          = $mediaRepositoryFile;
	}
    public function update($key,Request $request){
        $data=$this->categoryRepository->update($key,$request);
        if(!$data){
            return $this->categoryRepository->showErrors();
        }
        return response()->json($data, 200);
    }
    //default       :   get all record
    //lang          :   string
    //is_public     :   [0,1]
    //date:         :   asc,desc:default
    //key           :   string: get children category
    //search        :   string:
    //option       :   0->default get all, 1 ->get id and name;
    public function get(Request $request){
        if($request["option"]){
            $this->option=$request["option"];
        }
        $data=$this->categoryRepository->getData($request);
        if(!$data){
            return response()->json($data, 500);
        }
        $data=$data->paginate(config("category.paginate"))->appends(request()->query());

        $data->getCollection()->transform(function($value){
            $type=$this->categoryRepository->getTypeCategory($value->type);
            if($this->option==1){
                return [
                    "id"=>$value->id,
                    "name"=>str_limit($value->name,config("category.length_name_crop"),"..."),
                ];
            }
            return [
                "id"=>$value->id,
                "lang"=>$value->lang,
                "type"=>$type,
                "name"=>str_limit($value->name,config("category.length_name_crop"),"..."),
                "key"=>$value->key,
                "is_public"=>$value->is_public,
                "date"=>(($value->updated_at==null)?$value->created_at->toDateTimeString():$value->updated_at->toDateTimeString()),
                "child"=>$this->categoryRepository->countChildren($value->id),
                "post"=>$this->categoryRepository->countPosts($value->id)
            ];
        });
       return response()->json($data, 200);
    }
    //get breadcrumb
    public function getBreadcrumb(Request $request){
	    $key  = ($request['key'])?$request['key']:null;
	    if(is_null($key)){
            return response()->json("",200);
        }
	    $data=$this->categoryRepository->breadcrumb($key);
	    if($data){
            return response()->json($data, 200);
        }
        return response()->json($data, 500);
    }
    //create category
    public function store(Request $request){
	    $data=$this->categoryRepository->createCategory($request);
	    if($data){
            return response()->json($data, 200);
        }
        return response()->json($data, 500);
    }
    //get item category
    public function getItem($key,Request $request){
        $data=$this->categoryRepository->getItem($key,$request['option']);
        if($data){
            return response()->json($data, 200);
        }
        return response()->json($data, 500);
    }

    //upload avatar

    public function uploadAvatar(Request $request){
        $key= $request['key'];
        $files=$request['image'];
        $name=$request['name'];
        $category=$this->categoryRepository->convertKey($key);
        if(!$category){
            return abort(404);
        }
        $id=$category->id;
        if(is_null($category->avatar)){
            $media_files_id=$this->mediaRepositoryFile->uploadImage($files,$name);
            $this->categoryRepository->updateAvatarDB($id,$media_files_id);
        }else{
            $media_files_id=$this->mediaRepositoryFile->updateUploadImage($files,$name,$category->avatar);
        }
        return response()->json([
            "url"=>$this->mediaRepositoryFile->getPathFile($media_files_id)
        ]);
    }
}