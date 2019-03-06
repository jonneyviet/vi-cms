<?php
use Illuminate\Routing\Router;
$router->group(['prefix' => '/core'], function (Router $router) {
    $router->get('/lang',[
        "uses"=>"ApiCoreController@lang",
        "middleware"=>[]
    ]);
    $router->get('/setting',[
        "uses"=>"ApiCoreController@getSetting",
        "middleware"=>[]
    ])->where('key', '[A-Za-z0-9]+');
    $router->get('/lang.js', function () {
	    $strings = Cache::rememberForever('lang.js', function () {
	        $lang = config('app.locale');

	        $files   = glob(resource_path('lang/' . $lang . '/*.php'));
	        $strings = [];

	        foreach ($files as $file) {
	            $name           = basename($file, '.php');
	            $strings[$name] = require $file;
	        }

	        return $strings;
	    });

	    header('Content-Type: text/javascript');
	    echo('window.i18n = ' . json_encode($strings) . ';');
	    exit();
	})->name('admin.lang');
    $router->get('/setting.js',[
        "uses"=>"ApiCoreController@setting",
        "middleware"=>[]
    ]);
});
