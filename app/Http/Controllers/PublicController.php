<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;

class PublicController extends Controller
{

    public function __construct()
    {

    }
    public function uri($slug=null){
        /*
            Template
         */
        $template_root=config("core.template");
        /*
            Data send to views
         */
        $data=[];
        /*
            List url get to databas or cache;
         */
        $data_url=[
            "tin-tuc/tin-tuc-moi-nhat-2017"=>[
                "page"=>null,
                "post"=>null,
                "category"=>"211",
                "Product"=>null,
                "template"=>"category_2"
            ],
            "xa-hoi-hoa-2017"=>[
                "page"=>null,
                "post"=>"500",
                "category"=>null,
                "Product"=>null,
                "template"=>"post_1"
            ]
        ];
        if(array_has($data_url,$slug)){
            $template=array_get($data_url,$slug);
            $folder=(array_keys(array_filter($template)));
            return view(ucfirst($template_root).".".$folder[0].".".$template["template"],compact($data));
        }
        return abort(404);
    }
    private function getListlink(){
        /*
        Get all link for slug;
         */
    }
    private function getData($module,$id){
        /*
        Get data for view
         */
      $data=[
        "head"=>[
            "title"=>"",
            "description"=>"",
            "keyword"=>"",
            "image"=>"",
            "type"=>"",
            "url"=>"",
            "canonical"=>"",
            "script"=>"",
        ],
      ];
      return $data;
    }

}
