<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Core Mode
    |--------------------------------------------------------------------------
     */
    'admin-prefix'           => "admin",
    'template'               => env('TEMPLATE_SETTING', 'default'),
    'path'                   =>[],
    'packages' 				 =>[
    	"category"=>[
    		"alias"          => "Category",
            "icon"           =>"glyphicon-tasks",
            "link"           =>"/admin/category",
		    "active"         =>1,
            "sort"           =>1,
    	],
    ]
];
