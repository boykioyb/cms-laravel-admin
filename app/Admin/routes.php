<?php

use Illuminate\Routing\Router;

//Admin::registerAuthRoutes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => 'Encore\Admin\Controllers',
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->resource('auth/users', 'UserController');
    $router->resource('auth/roles', 'RoleController');
    $router->resource('auth/permissions', 'PermissionController');
    $router->resource('auth/menu', 'MenuController', ['except' => ['create']]);
    $router->resource('auth/logs', 'LogController', ['only' => ['index', 'destroy']]);

    $router->get('auth/login', 'AuthController@getLogin');
    $router->post('auth/login', 'AuthController@postLogin');
    $router->get('auth/logout', 'AuthController@getLogout');
    $router->get('auth/setting', 'AuthController@getSetting');
    $router->put('auth/setting', 'AuthController@putSetting');

});

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    $router->get('/listphones/formImport', 'ListPhoneController@formImport')->name('formImport');
    $router->post('/listphones/saveImport', 'ListPhoneController@saveImport')->name('saveImport');

    $router->get('/listphones/formImportEvict', 'ListPhoneController@formImportEvict')->name('formImportEvict');
    $router->post('/listphones/saveImportEvict', 'ListPhoneController@saveImportEvict')->name('saveImportEvict');

    $router->post('/listphones/evict', 'ListPhoneController@evict')->name('evict');

    $router->get('/permissions/autogenerate', 'PermissionController@autoGenerate')->name('autogenerate');

    $router->resources([
        'listphones' => ListPhoneController::class,
        'sales' => SalesController::class,
        'statistics' => StatisticsController::class,
        'permissions' => PermissionController::class,
        'roles' => RoleController::class,
    ]);

});
