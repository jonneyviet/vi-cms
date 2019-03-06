<?php
namespace Packages\Media\Controller\Admin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Image\Facades\Image;
use Packages\Core\Controllers\Controller;
use Packages\Media\Repositories\Contracts\FolderRepositoryInterface;
use Auth;
class MediaController extends Controller
{

    protected $mediaRepositoryFolder;

    public function  __construct(FolderRepositoryInterface $mediaRepositoryFolder){
        $this->mediaRepositoryFolder        = $mediaRepositoryFolder;
        
    }
    public function index()
    {
        return view("core::index");
    }
}