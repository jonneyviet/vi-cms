<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Core Mode
    |--------------------------------------------------------------------------
     */
    'admin-prefix'            => "admin",
    'template'            => env('TEMPLATE_SETTING', 'default'),
    'public_folder_admin'            => env('PUBLIC_FOLDER_ADMIN', ''),
    'packages' 				=>[
    	"category"=>[
    		"alias"=> "Category",
		    "active"=> 1,
    	],

    ],
    "error_404"=>"Not found page",

];
