<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->get('settings', 'SettingController@index');
    $router->resource('blogs', BlogController::class);
    $router->resource('articles', ArticleController::class);
    $router->resource('datelines', DatelineController::class);
    $router->resource('comments', CommentController::class);
});
