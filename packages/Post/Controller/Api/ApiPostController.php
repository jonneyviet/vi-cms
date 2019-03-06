<?php
namespace Packages\Post\Controller\Api;

use Packages\Core\Controllers\Controller;
use Illuminate\Http\Request;
use Packages\Post\Repositories\Contracts\PostRepositoryInterface;
use Packages\Category\Repositories\Contracts\CategoryRepositoryInterface;
use Packages\Media\Repositories\Contracts\FileRepositoryInterface;

class ApiPostController extends Controller
{
	protected $postRepository;
	protected $categoryRepository;
    protected $mediaRepositoryFile;

	public function __construct(PostRepositoryInterface $postRepository,CategoryRepositoryInterface $categoryRepository,FileRepositoryInterface $mediaRepositoryFile){
		 $this->postRepository        	    = $postRepository;
		 $this->categoryRepository        		= $categoryRepository;
         $this->mediaRepositoryFile          = $mediaRepositoryFile;
	}
    public function getAll(Request $request)
    {
        $key  = $request["key"];
        return $this->postRepository->getAll($key);
    }
    public function create(Request $request){
        $name                = $request["name"];
        $category            = $request["category"];
        if(is_array($category)){
            if(array_has($category,'id')){
                $category_id=$category["id"];
                $categoryData=$this->categoryRepository->convertId($category_id);
                if($categoryData){
                    return $this->postRepository->createPost($name,$categoryData);
                }
            }
        }
        return abort(404);
    }
    public function search(Request $request){


    }
    public function getType(Request $request){
        return $this->postRepository->getType($request['key'],$request['option']);
    }
    public function update(Request $request){
        $data = $request['data'];
        return $this->postRepository->update($data);
    }

    public function uploadAvatar(Request $request){
	    $key= $request['key'];
	    $files=$request['image'];
	    $name=$request['name'];
        $post=$this->postRepository->convertKey($key);
        if($post){
            $id=$post->id;
            $media_files_id=$this->mediaRepositoryFile->uploadImage($files,$name);
            $this->postRepository->uploadImageAvatar($id,$media_files_id);
            return response()->json([
                "url"=>$this->mediaRepositoryFile->getPathFile($media_files_id)
            ]);
        }
        return abort(404);
    }
}