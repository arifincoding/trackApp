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
$router->delete('/employes/{id}','EmployeeController@deleteEmployee');

$router->post('/employes/{id}/technician/responbility','ResponbilityController@newTechnicianResponbilities');
$router->delete('/employes/technician/responbility/{id}','ResponbilityController@delete');

$router->post('/services','ServiceController@newService');
$router->get('/services','ServiceController@getListService');
$router->get('/services/{id}','ServiceController@getServiceById');
$router->put('/services/{id}','ServiceController@updateService');
$router->delete('/services/{id}','ServiceController@deleteService');

$router->post('/services/{id}/diagnosas','DiagnosaController@newDiagnosaByIdService');
$router->get('/services/{id}/diagnosas','DiagnosaController@getListDiagnosaByIdService');
$router->get('/services/diagnosas/{id}','DiagnosaController@getDiagnosaById');
$router->put('/services/diagnosas/{id}','DiagnosaController@updateDiagnosa');
$router->delete('/services/diagnosas/{id}','DiagnosaController@deleteDiagnosa');

$router->post('/services/{id}/warranty','WarrantyController@newWarrantyByIdService');
$router->get('/services/{id}/warranty','WarrantyController@getServiceWarrantyByIdService');
$router->put('/services/warranty/{id}','WarrantyController@updateWarranty');
$router->delete('/services/warranty/{id}','WarrantyController@deleteWarranty');

$router->post('/categories','CategoryController@newCategory');
$router->get('/categories','CategoryController@getListCategory');
$router->get('/categories/{id}','CategoryController@getCategoryById');
$router->put('/categories/{id}','CategoryController@updateCategory');
$router->delete('/categories/{id}','CategoryController@deleteCategory');

$router->post('/user/login','AuthController@login');
$router->get('/user/{username}','UserController@getUserByUsername');

$router->group(['prefix'=>'','middleware'=>['auth','role:developer,teknisi']],function () use ($router){
    // $router->get('/categories','CategoryController@getListCategory');
    $router->put('user/change-password','UserController@changeMyPassword');
});

?>