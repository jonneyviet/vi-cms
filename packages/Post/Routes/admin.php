<?php
use Illuminate\Routing\Router;
$router->group(['prefix' => '/post'], function (Router $router) {
    $router->get('/{key?}/{key2?}',[
        "as"=>"admin.post.index",
        "uses"=>"PostController@index",
        "middleware"=>[]
    ])->where('key', '[A-Za-z0-9]+');
});
