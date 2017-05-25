<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\ActivationService;
use App\Models\Application;
use App\Models\Approval;
use App\Models\ClientFirstForm;
use App\Models\FormTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\ApplicationStep;
use App\MModels\Form;
use App\MModels\Field;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $vars;

    public function __construct()
    {
        parent::__construct();
        $this->vars = new \stdClass();
        $this->middleware(function ($request, $next) {
            $user = \Auth::user()->authorizeRoles(['admin', 'staff', 'client']);;
            return $next($request);
        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Auth::user()->verified) {
            $activation = new ActivationService();
            return view('homes.wait_emailverify', 
                [
                    'pageInfo' => $this->pageInfo,
                    'user' => Auth::user(),
                    'token' => $activation->getActivation(Auth::user())->token
                ]
            );
        }

        $userType = Auth::user()->roles[0]->name;

        $this->pageInfo->title              = Auth::user()->name."'s".' Dashboard';
        $this->pageInfo->category->title    = $userType;
        $this->pageInfo->subCategory->title = 'Homepage';
        $this->vars->userType = $userType;

        if($userType === "client"){
            $application = Client::where("user_id", Auth::id())->first()->application()->first();
            if(isset($application)){
                if($application->status == '0' || $application->status == 'wt_payment'){
                    return view('homes.wait_approval', ['pageInfo' => $this->pageInfo]);
                }
                return $this->applicationDashboard($request, $application->id);
            }
        }
        
        $this->vars->activeApplications = Application::where('status', '1')->get();
        $this->vars->inactiveApplications = Application::whereIn('status', ['0', 'wt_payment'])->get();

        return view('homes.admin', ['vars' => $this->vars, 'pageInfo' => $this->pageInfo]);
    }

    public function applicationDashboard(Request $request, $id)
    {
        $application = Application::with(['steps'])->find($id);
        $user = Auth::user();

        if ( $application->status == 'wt_firstform' ) {
            $this->pageInfo->title              = $user->name."'s".' Registration';
            $this->pageInfo->title              = Auth::user()->name."'s".' Registration';
            $this->pageInfo->category->title    = 'Registration';
            $this->pageInfo->subCategory->title = 'Form';
            $this->vars->userType = '$userType';
            $client = $user->client;
            $form = $client->firstForm;

            return view('applications.first_form',[
                'application'   => $application,
                'pageInfo'      => $this->pageInfo,
                'withAction'    => true,
                'form'          => $form,
            ]);

        }
        else
        {
            /*
             * Verify if current user has some responsibility about this step
             * */
            $currentStep = $application->steps->where("status", "current")->first();

            if($currentStep===null){
                $currentStep = $application->steps->first();
            }

            $currentUserType = $application->users()->where('user_id', $user->id)->get();

            if ($currentUserType->count() == 1)
            {
                $currentUserType = $currentUserType->first();
                $isResponsible = ($currentStep->responsible == $currentUserType->user_type);
            }
            else
            {
                $isResponsible = $currentUserType->where('user_type', $currentStep->responsible)->first();
            }
            /*
             * End verify responsible
             * */
            $stepsWithForm =  $application->steps->where("morphs_from", FormTemplate::class )->all(); //$this->_getStepsForm($application->steps, $currentStep);
            $approvalWithReport = $application->steps->where('morphs_from', Approval::class)->where('approval.has_report', '1')->all();
            $reports = array();

            foreach($approvalWithReport as $approval){
                $step = ApplicationStep::findOrFail($approval->id);
                if($step->Approval->report!=null){
                    array_push($reports, array('stepId' => $approval->id, 'report' => $step->Approval->report));
                }
            }

            $errorsFormsFields = $this->_getLastFormErrorsField($currentStep);
            return view('homes.application', [
                'pageInfo'              => $this->pageInfo,
                'application'           => $application,
                'stepsWithForm'         => $stepsWithForm,
                'approvalWithReport'    => $reports,
                'currentStep'           => $currentStep,
                'isResponsible'         => $isResponsible,
                'errorsFormsFields'     => $errorsFormsFields
            ]);
        }
    }
    private function _getStepsForm($steps, $currentStep){

        if($steps->where("morphs_from", FormTemplate::class )->all() > 0)
        {
            $stepsForm = array();
            foreach ($steps->where("morphs_from", FormTemplate::class )->orderBy("ordination", "asc")->all() as $step) {
                array_push($stepsForm, $step);
            }
        }
        if( $step->previousStep() !== null){
            return $this->_getLastFormErrorsField($step->previousStep());
        }
    }
    private function _getLastFormErrorsField($step){
        if($step->morphs_from === FormTemplate::class)
        {
            $errors = array();
            foreach($step->forms as $form){
                $f = Form::with(array('containers', 'containers.config', 'containers.fields', 'containers.fields.comments',
                    'containers.fields.setting', 'containers.fields.setting.rule', 'containers.fields.setting.rule.conditions') )->findOrFail($form->mform_id);
                array_push($errors, array("formId" => $form->form_templates_id, "containers" => $this->_getErrorsField($f)));
            }
            return $errors;
        }
        if( $step->previousStep() !== null){
            return $this->_getLastFormErrorsField($step->previousStep());
        }
    }

    private function _getErrorsField($form){
        $errors = array();
        try{
            foreach ($form->containers as $i => $c){
                foreach($c->fields as $k => $v){
                    if(isset($v->setting->error) && $v->setting->error === true){
                        array_push($errors, Field::find($v->_id));
                    }
                }
            }
            return $errors;
        }catch (Exception $e){
            return null;
        }
    }
}
