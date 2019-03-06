<?php
use Illuminate\Routing\Router;

$router->group(['prefix' => '/'], function (Router $router) {

    $router->get('/',[
    	"as"=>"admin.core.index",
    	"uses"=>"AppController@index",
    	"middleware"=>[]
    ]);
    $router->get('/logout',"AuthController@logout");
});
