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

$router->post('/employes','EmployeeController@createEmployee');
$router->put('/employes/{id}','EmployeeController@updateEmployee');
$router->put('/employes/{id}/status','EmployeeController@changeStatusEmployee');
$router->get('/employes','EmployeeController@getListEmployee');
$router->get('/employes/{id}','EmployeeController@getEmployeeById');
$router->post('/services','ServiceController@newService');
$router->get('/services','ServiceController@getListService');
$router->get('/services/{id}','ServiceController@getServiceById');
$router->post('/services/{id}/diagnosas','ServiceController@newServiceDiagnosa');
$router->get('/services/{id}/diagnosas','ServiceController@getListServiceDiagnosa');
$router->post('/services/{id}/warranty','ServiceController@newServiceWarranty');
$router->get('/services/{id}/warranty','ServiceController@getServiceWarranty');
$router->post('/categories','CategoryController@newCategory');
$router->get('/categories','CategoryController@getListCategory');
$router->get('/categories/{id}','CategoryController@getCategoryById');
$router->put('/categories/{id}','CategoryController@updateCategory');
$router->delete('/categories/{id}','CategoryController@deleteCategory');
$router->post('/user/login','AuthController@login');
$router->get('/user/{username}','UserController@getUserByUsername');
$router->post('/employes/{id}/technician/responbility','EmployeeController@newTechnicianResponbilities');

$router->group(['prefix'=>'','middleware'=>['auth','role:developer,teknisi']],function () use ($router){
    // $router->get('/categories','CategoryController@getListCategory');
});

?>