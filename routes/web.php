<?php
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Jenssegers\Mongodb\Eloquent\Model;
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

Route::get('register/confirmation/{token}', 'Auth\RegisterController@emailConfirmation');
Route::get('register/confirmation/resend/{token}/{id}', 'Auth\RegisterController@resendConfirmationToken');

Auth::routes();
Route::get('/test', function (Request $request) {
    
});

Route::group(['middleware' => 'auth'], function(){

    Route::get('/storage/{path}/{file}', function(\Illuminate\Http\Request $request, $path, $file){
        return response()->download(storage_path('app/public/'.$path.'/'.$file));
    });

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

    Route::get('/application/{id}/dashboard',  function(\Illuminate\Http\Request $request, $id){
        $home = new \App\Http\Controllers\HomeController();
        return $home->applicationDashboard($request, $id);
    });
    Route::get('/applications/{id}/settings',  'ApplicationsController@settings');
    Route::post('/applications/{id}/saveStepsPosition', 'ApplicationsController@saveStepsPosition');
    Route::post('/applications/{id}/changeStepStatus', 'ApplicationsController@changeStepStatus');

    Route::get('/applications/{id}/payment/first_form', 'ClientFirstFormController@staffView');
    Route::post('/applications/client/first_form', 'ClientFirstFormController@firstFormSave');

    Route::get('/applications/{id}/payment/{action}', 'ApplicationsController@validatePayment');

    Route::post('/steps/saveDefaultStepsPosition', 'StepsController@saveDefaultStepsPosition');

    Route::get('/applications/step/{id}',  'StepsController@appStep');

    Route::get('/forms/{id}/clone',  'FormsController@create');
    Route::get('/forms/{id}/mongo',  'FormsController@mongo');



    Route::get('/workflow/step/{id}/show/',  'WorkflowController@show');

    Route::post('/workflow/saveStepForm',  'WorkflowController@saveStepForm');
    Route::post('/workflow/saveApproval',  'WorkflowController@stepActions');

    Route::get('/workflow/step/{id}/{formId}/show/',  'WorkflowController@show');
    Route::post('/workflow/saveStepForm',  'WorkflowController@saveStepForm');
    Route::post('/workflow/saveApproval',  'WorkflowController@stepActions');

    Route::post('/workflow/updateFormField',  'WorkflowController@updateFormField');
    Route::post('/workflow/addFieldComment',  'WorkflowController@addFieldComment');

    Route::get('/usersync',  'SystemUsersController@syncUsers');

    Route::post('/upload-files', 'Controller@uploadFiles');

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
});