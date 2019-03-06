<?php

namespace Packages\Core\Controllers\Admin;

use Packages\Core\Controllers\Controller;

class AppController extends Controller
{
    public function __construct()
    {

    }
    public  function index(){
    	return view("core::index");
    }
}
