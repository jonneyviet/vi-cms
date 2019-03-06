<?php
use Illuminate\Routing\Router;
$router->group(['prefix' => '/translation'], function (Router $router) {
    $router->get('/',[
    	"as"=>"admin.translation.index",
    	"uses"=>"TranslationController@index",
    	"middleware"=>[]
    ]);
});
