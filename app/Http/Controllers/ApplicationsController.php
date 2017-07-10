<?php

namespace App\Http\Controllers;
use App\Mail\AuthEmails;
use App\MModels\Form;
use App\Models\ApplicationStep;
use App\Models\ApplicationStepApprovals;
use App\Models\ApplicationUserTypes;
use App\Models\ApplicationUsesEmail;
use App\Models\Approval;
use App\Models\ContinuousCompliance;
use App\Models\FormTemplate;
use App\Models\Step;
use App\Models\SysContinuousCompliance;
use App\Models\UsesEmail;
use Illuminate\Http\Response;
//use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\User;
use App\Models\UserType;
use App\Models\UserApplication;
use Illuminate\Support\Facades\Validator;

class ApplicationsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $applications;
    private $staffs;
    private $userTypes;
    private $usersApplication;
    public $vars;

    private $rules = [
//        'staff_id'    => 'required',
        'users_application' => 'required|users_application.needs_client',
        'description' => 'required|min:3|max:255',
    ];

    public function __construct()
    {
        parent::__construct();

//        $this->middleware(function ($request, $next) {
//            $user = \Auth::user()->authorizeRoles(['admin', 'staff']);
//            return $next($request);
//        });

        $this->applications     = Application::all();
        $this->userTypes        = UserType::All();

        foreach(User::all() as $u){
            if($u->hasRole("staff"))
                $this->staffs[] = $u;
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->pageInfo->title              = 'All Applications';
        $this->pageInfo->category->title    = 'Applications';
        $this->pageInfo->subCategory->title = 'List';

        $home = new HomeController();
        return $home->index($request);
    }

    public function edit(Request $request, $id)
    {
        $this->pageInfo->title              = 'Application Steps';
        $this->pageInfo->category->title    = 'Application';
        $this->pageInfo->subCategory->title = 'Edit Steps';

        $this->usersApplication = UserApplication::where('application_id', $id)->get();
        $application = Application::FindOrFail($id);

        if ($application->status == '1') 
        {
            if (app('env') != 'local')
            {
                return redirect('/application/'.$application->id.'/dashboard');
            }
            else
            {
                $this->pageInfo->title = 'ALREADY APPROVED - ONLY LOCAL';
            }
        }
        
        $steps = $application->steps()->with(['usesEmails', 'usesEmails.receivedBy', 'usesEmails.template'])->orderBy('ordination')->get();

        /*
         * After 3 years, the application will be cloned by a server service as a new application, becoming a new application, wainting payment and etc, but,
         * with steps already configured
         * */
        if ($application->type == 'cloned' && $application->status == '0') {
            //TODO
        }
        
        /*
         * If an application doesn't have steps, it's a new register and is waiting to system user approve
         * */
        if ($steps->isEmpty()) {
            //dd('Waiting payment');
            $this->pageInfo->title              = 'New register waiting payment';
            $this->pageInfo->category->title    = 'Application';
            $this->pageInfo->subCategory->title = 'Waiting';

            return view('applications.new_register',
                [
                    'application' => $application,
                    'pageInfo'          => $this->pageInfo
                ]
            );
        }


        return view('applications.form',
            [
                'application'       => $application,
                'staffs'            => $this->staffs,
                'userTypes'         => $this->userTypes,
                'usersApplication'  => $this->usersApplication,
                'steps'             => $steps,
                'pageInfo'          => $this->pageInfo
            ]
        );
    }

    public function validatePayment($id, $action)
    {
        $application = Application::findOrFail($id);

        switch ($action)
        {
            case 'allow';
                $application->status = '0';
                $application->save();
                ApplicationsController::cloneApplication($application, $application->client->user);
                $view = '/applications/'.$id.'/edit';
                break;

            case 'deny';
                $application->status = 'denied';
                $application->save();
                $user = $application->client->user;

                Mail::to($application->client->user)->send(
                    new AuthEmails('denyApp', [
                        'client'    => $application->client,
                        'user'      => $user
                    ])
                );
                $view = '/';
                break;

            case 'delete':
                $application->client->user->forceDelete();
                $application->forceDelete();
                $view = '/';
                break;

            default:
                throw new \Error('Action missing');
                break;
        }

        return redirect()->to($view);
    }

    /*
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, $id, Response $res)
    {
        Validator::extend('users_application.needs_client', function ($attribute, $value, $parameters, $validator) {

            if ( !is_array($value) ) {
                return false;
            }

            $request = Request::capture();

            $arrTypes = [];
            foreach($request->users_application as $uApp){
                $user_type = trim(explode(",", $uApp)[1]);
                array_push($arrTypes, ApplicationUserTypes::where('id', $user_type)->first()->slug);
            }

            if( array_search('client', $arrTypes) === false ){
                return false;
            }

            return true;
        });

        $validator = Validator::make($request->all(), $this->rules)->validate();

        \DB::beginTransaction();
        try{
            $application = Application::where('id', $id)->first();

            $request->offsetUnset('_token');

            UserApplication::where('application_id', $id)->delete();
            foreach($request->users_application as $uApp){
                $_arrUApp = explode(",", $uApp);
                $userApplication  = UserApplication::create([
                    'application_id'  => $id,
                    'user_id'         => trim(explode(",", $uApp)[0]),
                    'user_type'       => trim(explode(",", $uApp)[1]),
                ]);
            }

            if ( $request->status == '1' )
            {
                $user = $application->client->user;
                Mail::to($user)->send(
                    new AuthEmails('allowApp', [
                        'client'    => $application->client,
                        'user'      => $user
                    ])
                );
            }

            $request->offsetUnset('users_application');
            $request->offsetUnset('_method');
            $application->update($request->all());

        } catch(Exception $e){
            \Session::flash('error','Application update failed: ' . $e);
            \DB::rollBack();
            throw $e;
        }

        \DB::commit();
//        dd('everything is ok!');
        return Redirect::to('/applications/'.$id.'/edit');
    }

    public function settings(Request $request, $id)
    {
        $this->pageInfo->title              = 'Application Settings';
        $this->pageInfo->category->title    = 'Application';
        $this->pageInfo->subCategory->title = 'Edit Settings';

        $usersApplication = UserApplication::with(['user','appType'])->where('application_id', $id)->get();
        $application    = Application::FindOrFail($id);
        $userTypes      = $application->userTypes;
        $staffs         = User::all();
        $hasInactiveSteps = $application->steps()->where('status', '0')->get()->count();

        return view('applications.edit',
            [
                'application' => $application,
                'userTypes' => $userTypes,
                'staffs' => $staffs,
                'usersApplication'  => $usersApplication,
                'hasInactiveSteps'  => $hasInactiveSteps,
                'pageInfo' => $this->pageInfo,
            ]
        );
    }

    public function continuousComplianceNotAccredited(Request $request, $id)
    {
        $user = Auth::user();
        $application = Application::findOrFail($id);

        $this->pageInfo->title              = $application->client->company."'s".' Accredited Registration';
        $this->pageInfo->category->title    = $application->client->company;
        $this->pageInfo->category->link     = "/home";
        $this->pageInfo->subCategory->title = 'Continuous Compliance';

        $cComplianceForms = ContinuousCompliance::where('application_id', $application->id)->get();
        $cCompliancesRegistered = SysContinuousCompliance::where('application_id', $application->id)->orderBy('created_at', 'DESC')->get();

        return view('homes.application_completed', [
            'pageInfo'                  => $this->pageInfo,
            'application'               => $application,
            'cComplianceForms'          => $cComplianceForms,
            'cCompliancesRegistered'    => $cCompliancesRegistered,
            'isAdmin'                   => $user->hasRole(['admin','staff'])
        ]);
    }

    public function addContinuousCompliance(Request $request, $id)
    {
        $request->offsetSet('application_id', $id);
        $request->offsetUnset('_token');

        SysContinuousCompliance::create($request->all());

        return \redirect('/application/'.$id.'/dashboard');
//        $home = new \App\Http\Controllers\HomeController();
//        return $home->applicationDashboard($request, $id);
    }

    public function deleteContinuousCompliance(Request $request, $app_id, $comp_id)
    {
        $reg = SysContinuousCompliance::findOrFail($comp_id);
        $reg->delete();

        $home = new \App\Http\Controllers\HomeController();
        return \redirect('/application/'.$app_id.'/dashboard');
        //return $home->applicationDashboard($request, $app_id);
    }

    public function saveContinuousComplianceForm(Request $request, $appID, $relid)
    {
        $rel = ContinuousCompliance::findOrFail($relid);

        if (!$rel) {
            throw new \Error('Error. Relation not found. Please, contact system administrator');
        }

        $mForm = Form::find($rel->mongoform_id)->first();

//        dd($request->all());
//        dd($mForm);
//        $formsController = new FormsController();
//        $formsController->_saveContainers(\GuzzleHttp\json_decode($request->form_json), $relid);
//        dd('here!');
        $this->_updateFormToMongo(\GuzzleHttp\json_decode($request->form_json));

        $rel->status = 'sent';
        $rel->save();

        $arr = [
            'status' => true
        ];

        return json_encode($arr);
    }

    public function continuousComplianceForm(Request $request, $appID, $relid)
    {
        $rel = ContinuousCompliance::findOrFail($relid);
        $formID = $rel->form_template_id;

        if (!$rel) {
            throw new \Error('Error. Relation not found. Please, contact system administrator');
        }
        if ($rel->mongoform_id == null) {
            $mysql_form = FormTemplate::withTrashed()
                ->with([
                    'containers',
                    'containers.fields',
                    'containers.fields.comments'
                ])
                ->findOrFail($formID);

            $newMongoForm = $this->_storeFormToMongo( $mysql_form );
            $rel->mongoform_id = $newMongoForm;
            $rel->save();
        }

        $rel->status = 'filling';
        $rel->save();

        $form = Form::with([
            'containers',
            'containers.config',
            'containers.fields',
            'containers.fields.comments',
            'containers.fields.setting',
            'containers.fields.setting.rule',
            'containers.fields.setting.rule.conditions'])
            ->findOrFail($rel->mongoform_id);

        $app = Application::findOrFail($appID);
        $isResponsible = true;

        return view('applications.continuous_compliance_form',[
            'pageInfo'      => $this->pageInfo,
            'application'   => $app,
            'isResponsible' => $isResponsible,
            'containers'    => $form->containers
        ]);
    }

    public function saveStepsPosition($appID, Request $request)
    {
        $app = Application::findOrFail($appID);

        if ($app->reset_at || $app->status == '1') {
            return json_encode(['status' => false, 'title' => 'Error', 'message' => 'This application can not be changed!', 'header' => 'alert-danger']);
        }

        \DB::beginTransaction();
        $previous_step = null;
        $i = 0;
        while ($i < count($request->ids))
        {
            $stepID = $request->ids[$i];

            $step = ApplicationStep::findOrFail($stepID);
            $step->previous_step = $previous_step;
            $step->ordination = $i;
            $step->save();
            $previous_step = $stepID;
            $i++;
        }
        \DB::commit();

        return json_encode(['status' => true, 'title' => 'Success', 'message' => 'This application has been changed!', 'header' => 'alert-success']);
    }

    public function changeStepStatus(Request $request)
    {
        $step           = ApplicationStep::where('id', $request->id)->first();
        $newStatus      = ($request->currentStatus == '1') ? '0' : '1';
        $step->status   = $newStatus;

        if ( $step->application->status == '1' ) {
            return json_encode([
               'reqStatus' => 'disallowed'
            ]);
        }

        switch ($step->morphs_from)
        {
            case FormTemplate::class:
                $rel = $step->SQLForms;
                $approved = !$rel->isEmpty();
                break;

            case Approval::class:
                $rel = $step->morphs_id;
                $approved = ($rel > 0) ? true : false;
                break;

            default:
                throw new \Error('Morphs_from is not defined or Step not found.');
                break;
        }

        if ($approved) {
            $step->save();
        }

        $res = [
            'approved' => $approved,
            'reqStatus' => true,
            'newStatus' => $newStatus
        ];
        
        return json_encode($res);
    }

    public function deleteStep(Request $request)
    {
        Step::where('id', $request->id)->delete();
        
        $res = [
            'status' => true
        ];

        return json_encode($res);
    }

    public static function cloneApplication(Application $application, User $user)
    {
        $uTypes = UserType::where('status', 1)->get();
//dd($uTypes);
        /*
        * Clone user types default with new ids.
        * */
        $uTypesClones = []; //temp relations between new id (will be generated in this loop) and old id.
        foreach ($uTypes as $cloneType)
        {
            $defaultID = $cloneType->id;
            unset($cloneType['id']);
            unset($cloneType['created_at']);
            unset($cloneType['updated_at']);

            $cloneType->setAttribute('application_id', $application->id);

            $newAppType = ApplicationUserTypes::create($cloneType->getAttributes());
            $uTypesClones[$defaultID] = $newAppType->id;

            if ($cloneType->slug == 'client')
            {
                $clientTypeID = $newAppType->id;
            }
        }

        /*
         * Create application user where his type is the last type found
         * */
        $appUsers = UserApplication::create([
            'application_id' => $application->id,
            'user_id' => $user->id,
            'user_type' => $clientTypeID,
        ]);

        /*
         * Clone default steps with new ID
         * */
        $arr = [];
        $defaultSteps = Step::where('status', 1)->orderBy('ordination')->get();
        $default_ids = [];
        
        foreach ($defaultSteps as $step)
        {
            $newRefID = null;
            if ($step->previous_step) {
                $newRefID = $default_ids[$step->previous_step];
            }

            //If the userType set as Inactive, define the first element of all userTypes (cloned) as Responsible.
            if (!array_key_exists($step->responsible, $uTypesClones))
            {
                $copy = $uTypesClones;
                $responsible = array_shift($copy);
            }else{
                $responsible = $uTypesClones[$step->responsible];
            }

            $dataNewStep = [
                'application_id'    => $application->id,
                'previous_step'     => $newRefID,
                'responsible'       => $responsible, //Keep the usertype relation with new id
                'title'             => $step->title,
                'description'       => $step->description,
                'ordination'        => $step->ordination,
                'status'            => '0',
                'morphs_from'       => $step->morphs_from,
                'morphs_id'         => $step->morphs_id,
            ];

            if (!$responsible) {
                dd($uTypesClones);
//                dd($dataNewStep);
            }


            $appSteps = ApplicationStep::create($dataNewStep);
            $default_ids[$step->id] = $appSteps->id;

            /*
             * Clone UsesEmails default with new IDs using default temporary relationship user types
             * */
            $emails = UsesEmail::where('step_id', $step->id)->get();

            $t = [];
            foreach ($emails as $clone)
            {
                $cloneID = $clone->received_by;
                unset($clone['id']);
                unset($clone['step_id']);
                unset($clone['received_by']);
                $clone->application_step_id = $appSteps->id;
                $newEmailRelation = new ApplicationUsesEmail($clone->getAttributes());
//dd(array_search($cloneID, $uTypesClones));
//dd($uTypesClones);
//                array_push($t,[$cloneID => array_key_exists($cloneID, $uTypesClones), $uTypesClones]);

                if ( array_key_exists($cloneID, $uTypesClones) )
                {
//                    dd($uTypesClones[$cloneID]);
                    $newEmailRelation->received_by = $uTypesClones[$cloneID]; //$newAppType->id;
                    $newEmailRelation->save();
                }

//                if ($uTypesClones[$cloneID])
//                {
//                }
            }
//                dd($t);
        }
    }
}
