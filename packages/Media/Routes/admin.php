<?php
use Illuminate\Routing\Router;
$router->group(['prefix' => '/media'], function (Router $router) {
    $router->get('/{key?}',[
    	"as"=>"admin.media.index",
    	"uses"=>"MediaController@index",
    	"middleware"=>[]
    ])->where('key', '[A-Za-z0-9]+');
});
