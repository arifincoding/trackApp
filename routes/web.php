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

$router->post('/services/{id}/warranty','WarrantyController@newWarrantyByIdService');
$router->get('/services/{id}/warranty','WarrantyController@getServiceWarrantyByIdService');
$router->put('/services/warranty/{id}','WarrantyController@updateWarranty');
$router->delete('/services/warranty/{id}','WarrantyController@deleteWarranty');

$router->post('/user/login','AuthController@login');

$router->group(['prefix'=>'','middleware'=>['auth','role:pemilik']],function () use ($router){

    // employee
    $router->post('/employes','EmployeeController@createEmployee');
    $router->put('/employes/{id}','EmployeeController@updateEmployee');
    $router->get('/employes','EmployeeController@getListEmployee');
    $router->get('/employes/{id}','EmployeeController@getEmployeeById');
    $router->delete('/employes/{id}','EmployeeController@deleteEmployee');

    // categories
    $router->post('/categories','CategoryController@newCategory');
    $router->get('/categories/{id}','CategoryController@getCategoryById');
    $router->put('/categories/{id}','CategoryController@updateCategory');
    $router->delete('/categories/{id}','CategoryController@deleteCategory');

    // brokens
    $router->put('/services/brokens/{id}/cost','BrokenController@updateBrokenCost');
    $router->put('/services/brokens/{id}/confirm','BrokenController@updateBrokenCofirmation');

    // services
    $router->put('/services/{id}/confirm-cost','ServiceController@setServiceConfirmCost');
    $router->put('/services/{id}/warranty','ServiceController@updateServiceWarranty');
    $router->put('/services/{id}/confirmation','ServiceController@setServiceConfirmation');

    // responbility
    $router->post('/employes/{id}/technician/responbilities','ResponbilityController@newTechnicianResponbilities');
    $router->delete('/employes/technician/responbilities/{id}','ResponbilityController@delete');
});

$router->group(['prefix'=>'','middleware'=>['auth','role:pemilik,customer service']],function () use ($router){

    // category
    $router->get('/categories','CategoryController@getListCategory');

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
    $router->get('/services/{id}/brokens','BrokenController@getListBrokenByIdService');
    $router->get('/services/brokens/{id}','BrokenController@getBrokenById');

    // user
    $router->post('/user/logout','AuthController@logout');
    $router->post('/user/refresh','AuthController@createRefreshToken');
    $router->put('user/change-password','UserController@changeMyPassword');
});

$router->group(['prefix'=>'','middleware'=>['auth','role:teknisi']],function() use ($router){
    // brokens
    $router->post('/services/{id}/brokens','BrokenController@newBrokenByIdService');
    $router->put('/services/brokens/{id}','BrokenController@updateBroken');
    $router->delete('/services/brokens/{id}','BrokenController@deleteBroken');

    // service
    $router->get('/services/queue','ServiceController@getServiceQueue');
    $router->get('/services/progress','ServiceController@getMyProgressService');
    $router->put('/services/{id}/status','ServiceController@updateServiceStatus');
});

$router->group(['prefix'=>'','middleware'=>['auth','role:pemilik,teknisi']],function() use ($router){
    // responbility
    $router->get('/employes/{id}/technician/responbilities','ResponbilityController@getTechnicianResponbilities');
});
?>