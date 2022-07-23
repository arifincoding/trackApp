<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/user/login','UserController@login');

$router->get('/services/{id}/track','ServiceController@getServiceTrack');

$router->group(['prefix'=>'','middleware'=>['auth','role:pemilik']],function () use ($router){

    // employee
    $router->post('/employes','UserController@create');
    $router->put('/employes/{id}','UserController@update');
    $router->get('/employes','UserController@all');
    $router->get('/employes/{id}','UserController@show');
    $router->delete('/employes/{id}','UserController@delete');
    $router->get('/employes/{id}/categories-not-in-responbility','CategoryController@getCategoryNotInResponbility');

    // categories
    $router->post('/categories','CategoryController@create');
    $router->get('/categories/{id}','CategoryController@show');
    $router->put('/categories/{id}','CategoryController@update');
    $router->delete('/categories/{id}','CategoryController@delete');

    // brokens
    $router->put('/services/brokens/{id}/cost','BrokenController@updateCost');
    $router->put('/services/brokens/{id}/confirm','BrokenController@updateCofirmation');

    // services
    $router->put('/services/{id}/confirm-cost','ServiceController@setConfirmCost');
    $router->put('/services/{id}/warranty','ServiceController@updateWarranty');
    $router->put('/services/{id}/confirmation','ServiceController@setConfirmation');

    // responbility
    $router->post('/employes/{id}/technician/responbilities','ResponbilityController@create');
    $router->delete('/employes/technician/responbilities/{id}','ResponbilityController@delete');
    $router->get('/chat/scan','WhatsappController@scan');
});

$router->group(['prefix'=>'','middleware'=>['auth','role:pemilik,customer service']],function () use ($router){

    // category
    $router->get('/categories','CategoryController@all');

    // service
    $router->get('/services','ServiceController@getListService');
    $router->post('/services','ServiceController@newService');
    $router->put('/services/{id}','ServiceController@updateService');
    $router->delete('/services/{id}','ServiceController@deleteService');
    $router->put('/services/{id}/take','ServiceController@setServiceTake');
});

$router->group(['prefix'=>'','middleware'=>['auth']],function () use ($router){

    // service
    $router->get('/services/{id}/detail','ServiceController@getServiceById');
    $router->get('/services/{id}/brokens','BrokenController@getListByIdService');
    $router->get('/services/brokens/{id}','BrokenController@getBrokenById');
    // user
    $router->post('/user/logout','UserController@logout');
    $router->post('/user/refresh','UserController@createRefreshToken');
    $router->put('/user/change-password','UserController@changePassword');
    $router->get('/user/account','UserController@getMyAccount');
    $router->put('/user/account','UserController@updateMyAccount');

    // chat
    $router->post('/services/{id}/chat','WhatsappController@chat');

    // history
    $router->post('/services/{id}/history','HistoryController@create');
});

$router->group(['prefix'=>'','middleware'=>['auth','role:teknisi']],function() use ($router){
    // brokens
    $router->post('/services/{id}/brokens','BrokenController@newByIdService');
    $router->put('/services/brokens/{id}','BrokenController@update');
    $router->delete('/services/brokens/{id}','BrokenController@delete');

    // service
    $router->get('/services/{id}/queue','ServiceController@getServiceQueue');
    $router->get('/services/{id}/progress','ServiceController@getProgressService');
    $router->put('/services/{id}/status','ServiceController@updateServiceStatus');
});

$router->group(['prefix'=>'','middleware'=>['auth','role:pemilik,teknisi']],function() use ($router){
    // responbility
    $router->get('/employes/{id}/technician/responbilities','ResponbilityController@all');
});
?>