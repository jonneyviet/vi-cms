<?php
namespace Packages\Translation\Controller\Admin;

use App\Http\Controllers\Controller;


class TranslationController extends Controller
{
    public function index()
    {
        //return "complete";
        return view("translation::index");
    }

}