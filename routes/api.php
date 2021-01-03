<?php

use App\Services\ScopeService as Scope;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['namespace' => 'Api'], function (Router $router) {

    $router->post('/register/principal', 'RegisterController@registerPrinciple');

    $router->post('/schools/{school}/teachers', 'RegisterController@registerTeacher');

    $router->group(['middleware' => 'auth:api'], function (Router $router) {
        $router->get('/me', 'ApiController@me');
        $router->get('/schools/{school}/teachers', 'SchoolController@getTeachers');
        $router->group(['middleware' => Scope::middlewarePrincipal()], function (Router $router) {
            $router->get('/schools', 'SchoolController@getSchools');
            $router->get('/schools/{school}/invite_url', 'SchoolController@makeInviteUrl');
            $router->post('/schools', 'SchoolController@apply');
        });

        $router->group(['middleware' => Scope::middlewareTeacher()], function (Router $router) {
            $router->get('/schools/{school}/students', 'SchoolController@getStudents');
            $router->post('/schools/{school}/students', 'SchoolController@createStudent');
        });

        $router->group(['middleware' => Scope::middlewareStudent()], function (Router $router) {
            $router->post('/relation/{teacher}}/focus', 'RelationController@focus');
            $router->delete('/relation/{teacher}}/focus', 'RelationController@unfocus');
        });
    });
});

