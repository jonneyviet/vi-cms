<?php
use Illuminate\Routing\Router;
$router->group(['prefix' => '/category'], function (Router $router) {
    $router->get('/{key?}/{key2?}',[
        "as"=>"admin.category.index",
        "uses"=>"CategoryController@index",
        "middleware"=>[]
    ])->where('key', '[A-Za-z0-9]+');
});
