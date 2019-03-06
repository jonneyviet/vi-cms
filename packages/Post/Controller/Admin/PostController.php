<?php
namespace Packages\Post\Controller\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class PostController extends Controller
{
    public function index()
    {
        //return "complete";
        return view("core::index");
    }
}