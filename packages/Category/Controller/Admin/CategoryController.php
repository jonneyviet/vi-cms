<?php
namespace Packages\Category\Controller\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    public function index()
    {
        //return "complete";
        return view("core::index");
    }
}