<?php
use Illuminate\Routing\Router;
$router->group(['prefix' => '/category'], function (Router $router) {
    $router->get('/get',[
        "uses"=>"ApiCategoryController@get",
        "middleware"=>[]
    ]);
    $router->get('/getBreadcrumb',[
        "uses"=>"ApiCategoryController@getBreadcrumb",
        "middleware"=>[]
    ]);
    $router->get('/search',[
        "uses"=>"ApiCategoryController@search",
        "middleware"=>[]
    ]);
    $router->get('/getItem/{key}',[
        "uses"=>"ApiCategoryController@getItem",
        "middleware"=>[]
    ]);

    $router->post('/create',[
        "uses"=>"ApiCategoryController@store",
        "middleware"=>[]
    ]);
    $router->post('/update/{key}',[
        "uses"=>"ApiCategoryController@update",
        "middleware"=>[]
    ]);
    $router->post('/uploadAvatar',[
        "uses"=>"ApiCategoryController@uploadAvatar",
        "middleware"=>[]
    ]);
});
