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


Auth::routes();

Route::group(['middleware' => 'auth'], function(){

//    Route::get('/', function () {
//        return view('/home');
//    });

    Route::get('/', 'HomeController@index');
    Route::get('/home', 'HomeController@index');

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

    Route::resource('/users', 'SystemUsersController');
    Route::resource('/usertypes', 'UserTypesController');
    Route::resource('/steps', 'StepsController');
    Route::resource('/emails', 'EmailsController');
    Route::resource('/forms',  'FormsController');
    Route::resource('/screens', 'ScreensController');
    Route::resource('/approvals', 'ApprovalsController');
    Route::resource('/applications',  'ApplicationsController');

    Route::post('/step/delete',  'ApplicationsController@deleteStep');

    Route::get('/applications/{id}/settings',  'ApplicationsController@settings');
    Route::post('/applications/{id}/saveStepsPosition', 'ApplicationsController@saveStepsPosition');
    Route::post('/applications/{id}/changeStepStatus', 'ApplicationsController@changeStepStatus');
    
    Route::post('/steps/saveDefaultStepsPosition', 'StepsController@saveDefaultStepsPosition');

    Route::get('/applications/step/{id}',  'StepsController@appStep');

    Route::get('/forms/{id}/clone',  'FormsController@create');

    Route::post('/forms/comment', function (Request $request) {
        try{
            \App\Models\Comment::create([
                'userId'    => Auth::id(),
                'userName'  => Auth::user()->name,
                'text'      => $request->text,
                'field'     => $request->field,
            ]);
            return true;
        }catch (Exception $e){
            return false;
        }
    });

    Route::get('/emails/{id}/test', function (Request $request, $id) {
        try{
            $mail = \App\Models\EmailTemplate::find($id);
            return View::make('emails.view')->with("email", $mail)->withShortcodes();
        }catch (Exception $e){
            echo "false: " . $e;
        }
    });
});