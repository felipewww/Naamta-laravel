<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\ActivationService;
use App\Models\Application;
use App\Models\Approval;
use App\Models\ClientFirstForm;
use App\Models\ContinuousCompliance;
use App\Models\FormTemplate;
use App\Models\Report;
use App\Models\SysContinuousCompliance;
use App\Models\UserApplication;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\ApplicationStep;
use App\MModels\Form;
use App\MModels\Field;
use Illuminate\Support\Facades\Redirect;


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
        if (!Auth::user())
        {
            return Redirect::to('/login');
        }
        else
        {
            $this->middleware(function ($request, $next) {
                $user = \Auth::user()->authorizeRoles(['admin', 'staff', 'client']);;
                return $next($request);
            });
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Auth::user()) {
            return Redirect::to('/login');
        }
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
        
        $this->vars->completedApplications = Application::where('status', 'completed')->get();

        $this->vars->activeApplications = Application::where('status', '1')->get();

        foreach ($this->vars->activeApplications as &$app)
        {
            $currStep = $app->steps()->where('status', 'current')->first();
            if ( !$currStep )
            {
                $currStep = $app->steps()->where('status', '1')->first();
                $lastDateSubmit = 'None';
            }
            else
            {
                $previous = ApplicationStep::find($currStep->previous_step);

                if (!$previous) {
                    $lastDateSubmit = ' ';
                }else{
                    $lastDateSubmit = $previous->updated_at->toDateTimeString();
                }
            }
            $app->offsetSet('currStep', $currStep);
            $app->offsetSet('lastDateSubmit', $lastDateSubmit);
        }

        /*
         * Não da para exibir os firstforms pq o cliente pode ter ou nao preenchido e exibe o botão de approve e deny, antes de ter preenchido
         */
        $this->vars->inactiveApplications = Application::whereIn('status', ['0','wt_payment','denied', 'wt_firstform_validation'])->orderBy('created_at', 'DESC')->get();
        foreach ($this->vars->inactiveApplications as &$inApp)
        {
            switch ($inApp->status)
            {
                case '0':
                    $inApp->statusText = 'Waiting staff setup';
                    break;

                case 'wt_payment':
                    $inApp->statusText = 'Waiting First Form verification';
                    break;

                case 'denied':
                    $inApp->statusText = 'Denied, waiting resend';
                    break;
            }
        }

        return view('homes.admin', ['vars' => $this->vars, 'pageInfo' => $this->pageInfo]);
    }

    public function applicationDashboard(Request $request, $id)
    {
        $application = Application::with(['steps'])->find($id)->authorize();
        $user = Auth::user();

        if ( $application->status == 'wt_firstform' || $application->status == 'denied' )
        {
            $this->pageInfo->title              = $user->client->company."'s".' Registration';
            $this->pageInfo->category->title    = 'Registration';
            $this->pageInfo->subCategory->title = 'Form';

            $user = Auth::user();
            $form = \App\MModels\Form::with([
                'containers',
                'containers.config',
                'containers.fields',
                'containers.fields.comments',
                'containers.fields.setting',
                'containers.fields.setting.rule',
                'containers.fields.setting.rule.conditions'])
                ->findOrFail($user->client->mform_register_id);

            return view('applications.first_form')->with([
                'isResponsible' => true,
                'containers' => $form->containers,
                'pageInfo' => $this->pageInfo
            ]);

        }
        else if( $application->status == 'wt_firstform_validation' )
        {
            $this->pageInfo->title              = $user->client->company."'s".' Registration';
            $this->pageInfo->category->title    = 'Waiting validation';
            $this->pageInfo->subCategory->title = 'Form';

            return view('applications.wait_firstform_verify')->with([
                'pageInfo' => $this->pageInfo
            ]);
        }
        else if( $application->status == 'completed' )
        {
            $this->pageInfo->title              = $application->client->company."'s".' Accredited Registration';
            $this->pageInfo->category->title    = 'Client';
            $this->pageInfo->subCategory->title = 'Dashboard';

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

            //Starting... Get all forms related with all steps, including the current step
            $stepsWithForm =  $application->steps->where("morphs_from", FormTemplate::class )->all();
//            dd($stepsWithForm);
            foreach ($stepsWithForm as &$stepHasForm)
            {
                $thisForms = $stepHasForm->Forms()->get();
                foreach ($thisForms as &$relData)
                {
                    $mongoForm = Form::findOrFail($relData->mform_id);
                    $relData->offsetSet('mongoform', $mongoForm);
                }

                $stepHasForm->offsetSet('mongoForms', $thisForms);
            }

            $approvalWithReport = $application->steps->where('morphs_from', Approval::class)->where('approval.has_report', '1')->all();
            $reports = array();


            foreach($approvalWithReport as $approval)
            {
                $step = ApplicationStep::findOrFail($approval->id);

                $allReports = Report::where('application_steps_id', $step->id)->get();

                if($allReports != null)
                {
                    foreach ($allReports as $rep)
                    {
                        array_push($reports, array('stepId' => $approval->id, 'report' => $rep));
                    }
                }
            }

            $errorsFormsFields = $this->_getAllFormsErrorsField($currentStep);
            $cComplianceForms = ContinuousCompliance::where('application_id', $application->id)->get();

            return view('homes.application', [
                'pageInfo'              => $this->pageInfo,
                'application'           => $application,
                'stepsWithForm'         => $stepsWithForm,
                'approvalWithReport'    => $reports,
                'currentStep'           => $currentStep,
                'isResponsible'         => $isResponsible,
                'errorsFormsFields'     => $errorsFormsFields,
                'cComplianceForms'      => $cComplianceForms,
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
}
