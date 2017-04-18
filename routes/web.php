<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::get('/users', 'SystemUsersController@index');
Route::get('/users/create', 'SystemUsersController@create');
Route::get('/users/{id}/edit', 'SystemUsersController@edit');
Route::post('/users/{id}/edit', 'SystemUsersController@edit_post');
Route::post('/users/{id}/delete', 'SystemUsersController@delete');

Route::get('/userstype', 'UserTypesController@index');
Route::get('/userstype/create', 'UserTypesController@insert');
Route::post('/userstype/create', 'UserTypesController@insert_post');
Route::get('/userstype/{id}/edit', 'UserTypesController@update');
Route::post('/userstype/{id}/edit', 'UserTypesController@update_post');
Route::post('/userstype/{id}/delete', 'UserTypesController@delete');

Route::get('/emails', 'EmailsController@index');
Route::get('/emails/create', 'EmailsController@insert');
Route::post('/emails/create', 'EmailsController@insert_post');
Route::get('/emails/{id}/edit', 'EmailsController@update');
Route::post('/emails/{id}/edit', 'EmailsController@update_post');
Route::post('/emails/{id}/delete', 'EmailsController@delete');


Route::get('/callback', function (Request $request) {
    $http = new GuzzleHttp\Client;

    $response = $http->post('http://127.0.0.1/oauth/token', [
        'form_params' => [
            'grant_type' => 'authorization_code',
            'client_id' => 'client-id',
            'client_secret' => 'client-secret',
            'redirect_uri' => 'http://127.0.0.1/callback',
            'code' => $request->code,
        ],
    ]);

    return json_decode((string) $response->getBody(), true);
});

Route::resource('/step', 'StepController');
Route::resource('/email', 'EmailController');
