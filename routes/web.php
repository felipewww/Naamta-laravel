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

Route::get('/admin/php/info', function (\Illuminate\Http\Request $request){
    phpinfo();
});

Route::get('register/confirmation/{token}', 'Auth\RegisterController@emailConfirmation');
Route::get('register/confirmation/resend/{token}/{id}', 'Auth\RegisterController@resendConfirmationToken');


Auth::routes();
Route::get('/test', function (Request $request) {

});

Route::get('/formtest', function (Request $request) {

});

Route::group(['middleware' => 'auth'], function(){

    Route::post('/user/logout', function (\Illuminate\Http\Request $request){
        $arr = [
            'status' => 1
        ];
        \Illuminate\Support\Facades\Auth::logout();
        return json_encode($arr);
    });

    Route::get('/seemail/{email}', function(\Illuminate\Http\Request $request, $email){
        return App\Mail\SeeEmail::see($email);
    });

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

    Route::get('/client/{id}/profile', 'SystemUsersController@clientProfile');

    Route::post('/step/delete',  'ApplicationsController@deleteStep');

    Route::get('/application/{id}/dashboard',  function(\Illuminate\Http\Request $request, $id){
        $home = new \App\Http\Controllers\HomeController();
        return $home->applicationDashboard($request, $id);
    });

    //view of continuous forms when application isn't accredited yet
    //It's possible will be removed because on documentation says: "Enable continuos when applicant BECOMES ACCREDITED"...
    Route::get('/application/{id}/continuousCompliances', 'ApplicationsController@continuousComplianceNotAccredited');

    Route::post('/application/{id}/addContinuousCompliance', 'ApplicationsController@addContinuousCompliance');
    Route::get('/application/{id}/deleteContinuousCompliances/{cid}', 'ApplicationsController@deleteContinuousCompliance');

    Route::get('/applications/{id}/settings',  'ApplicationsController@settings');

    Route::get('/applications/{id}/continuous/{relID}',  'ApplicationsController@continuousComplianceForm');
    Route::post('/applications/{id}/continuous/{relID}',  'ApplicationsController@saveContinuousComplianceForm'); //send ajax form

    Route::post('/applications/{id}/saveStepsPosition', 'ApplicationsController@saveStepsPosition');
    Route::post('/applications/{id}/changeStepStatus', 'ApplicationsController@changeStepStatus');

    Route::get('/firstFormEdit', 'FormsController@firstFormEdit');

    Route::get('/applications/{id}/payment/first_form', 'ClientFirstFormController@staffView');
    Route::post('/applications/client/first_form', 'ClientFirstFormController@_firstFormSave');

    Route::get('/applications/{id}/payment/{action}', 'ApplicationsController@validatePayment');

    Route::post('/steps/saveDefaultStepsPosition', 'StepsController@saveDefaultStepsPosition');

    Route::get('/applications/step/{id}',  'StepsController@appStep');

    Route::get('/forms/{id}/clone',  'FormsController@create');
    Route::get('/forms/{id}/mongo',  'FormsController@mongo');



    Route::get('/workflow/step/{id}/show/',  'WorkflowController@show');
    Route::get('/workflow/step/{id}/approval/{reportID}',  'WorkflowController@showReport');

    Route::post('/workflow/gotoNextStep',  'WorkflowController@gotoNextStep');
//    Route::post('/workflow/saveStepForm',  'WorkflowController@saveStepForm');
//    Route::post('/workflow/saveApproval',  'WorkflowController@saveApproval');

    Route::post('/workflow/saveStepForm',  'WorkflowController@stepActions');
    Route::post('/workflow/saveApproval',  'WorkflowController@stepActions');

    Route::get('/workflow/step/{id}/{formId}/show/',  'WorkflowController@show');

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