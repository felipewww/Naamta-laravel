<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\ActivationService;
use App\Models\Application;
use App\Models\ApplicationUserTypes;
use App\Models\Approval;
use App\Models\ClientFirstForm;
use App\Models\ContinuousCompliance;
use App\Models\FormTemplate;
use App\Models\Report;
use App\Models\SysContinuousCompliance;
use App\Models\User;
use App\Models\UserApplication;
use Faker\Factory;
use Illuminate\Contracts\Session\Session;
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
//        dd('here');
        if (!Auth::user()) {
            return Redirect::to('/login');
        }

        if ( Auth::user()->isClient() )
        {
            if (!Auth::user()->verified) {

                $this->pageInfo->title = Auth::user()->client->company;
                $this->pageInfo->category->title    = "Applications";
                $this->pageInfo->subCategory->title =  "Waiting e-mail verification";

                $activation = new ActivationService();
                return view('homes.wait_emailverify',
                    [
                        'pageInfo' => $this->pageInfo,
                        'user' => Auth::user(),
                        'token' => $activation->getActivation(Auth::user())->token
                    ]
                );
            }
        }


        $user = Auth::user();
        $userType = $user->roles[0]->name;
        $this->vars->userType = $userType;

        $this->pageInfo->title              = 'Dashboard';
        $this->pageInfo->category->title    = "Applications";
        $this->pageInfo->category->link     = "/home";
        $this->pageInfo->subCategory->title =  Auth::user()->name . "'s";
        $this->vars->userType = $userType;

        if($userType == 'client'){
            $this->pageInfo->category->title    = $user->client->company."'s";
        }

        if($userType === "client"){
            $application = Client::where("user_id", Auth::id())->first()->application()->first();

            if(isset($application)){
                if($application->status == '0' || $application->status == 'wt_payment'){
                    return view('homes.wait_approval', ['pageInfo' => $this->pageInfo]);
                }
                return $this->applicationDashboard($request, $application->id);
            }else{
//                \Auth::user()->logout();
                Auth::logout();
//                return redirect()->to('/')->withErrors(['email' => "Your app is no longer available"]);
                return redirect('/login')->with('disallowed', "Your app is no longer available");
                //return 'No longer available';
            }
        }

        $appIds = UserApplication::where('user_id', Auth::user()->id)->with(['application','application.steps'])->get();

        if (Auth::user()->isAdmin())
        {
            $this->vars->completedApplications = Application::where('status', 'completed')->get();
            $this->vars->activeApplications = Application::where('status', '1')->get();
//            dd($this->vars->activeApplications);
        }
        else if(Auth::user()->isstaff())
        {
            $completedApplications = $appIds->where('application.status', 'completed');
            $this->vars->completedApplications = [];

            foreach ($completedApplications as $userApp)
            {
                array_push($this->vars->completedApplications, $userApp->application);
            }

            $activeApplications = $appIds->where('application.status', '1');

            $this->vars->activeApplications = [];
            foreach ($activeApplications as $userApp)
            {
                array_push($this->vars->activeApplications, $userApp->application);
            }
        }

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
//        $this->vars->inactiveApplications = Application::whereIn('status', ['0','wt_payment','denied', 'wt_firstform_validation'])->orderBy('created_at', 'DESC')->get();
        $this->vars->inactiveApplications = Application::whereIn('status', ['0','wt_payment', 'wt_firstform_validation'])->orderBy('created_at', 'DESC')->get();
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
            $this->pageInfo->title              = 'Registration';
            $this->pageInfo->category->title    = $user->client->company."'s";
            $this->pageInfo->category->link     = "/home";
            $this->pageInfo->subCategory->title = 'Registration Form';

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
            $this->pageInfo->title              = 'Registration';
            $this->pageInfo->category->title    = $user->client->company."'s";
            $this->pageInfo->category->link     = "/home";
            $this->pageInfo->subCategory->title = 'Waiting validation';

            return view('applications.wait_firstform_verify')->with([
                'pageInfo' => $this->pageInfo
            ]);
        }
        else if( $application->status == 'completed' )
        {
            $this->pageInfo->title              = 'Accredited Registration';
            $this->pageInfo->category->title    = $application->client->company."'s";
            $this->pageInfo->category->link     = "/home";
            $this->pageInfo->subCategory->title = 'Dashboard';

            $cComplianceForms = ContinuousCompliance::where('application_id', $application->id)->get();
            $cCompliancesRegistered = SysContinuousCompliance::where('application_id', $application->id)->orderBy('created_at', 'DESC')->get();

            $workflowInfo = $this->getApplicationWorkflowInfo($application);

            return view('homes.application_completed', [
                'pageInfo'                  => $this->pageInfo,
                'application'               => $application,
                'cComplianceForms'          => $cComplianceForms,
                'cCompliancesRegistered'    => $cCompliancesRegistered,
                'stepsWithForm'             => $workflowInfo['stepsWithForm'],
                'approvalWithReport'        => $workflowInfo['approvalWithReport'],
                'isAdmin'                   => $user->hasRole(['admin','staff'])
            ]);
        }
        else
        {
            $this->pageInfo->title              = 'Application';
            $this->pageInfo->category->title    = $application->client->company."'s";
            $this->pageInfo->category->link     = "/home";
            $this->pageInfo->subCategory->title = 'Dashboard';

            /*
             * Verify if current user has some responsibility about this step
             * */
            $currentStep = $application->steps->where("status", "current")->first();

            if (!$currentStep) {
                $currentStep = $application->steps->where("status", "1")->first();
            }

            $isResponsible = $currentStep->loggedUserIsStepResponsible();
            $userTypeResponsible = ApplicationUserTypes::where('id', $currentStep->responsible)->first();
            //dd($currentStep->responsible);
            $userResponsible = UserApplication::where('user_type', $currentStep->responsible)->get();

            $workflowInfo = $this->getApplicationWorkflowInfo($application);

            $errorsFormsFields = $this->_getAllFormsErrorsField($currentStep);
            $cComplianceForms = ContinuousCompliance::where('application_id', $application->id)->get();

            if (!$currentStep->previous_step) {
                $previousStep = new ApplicationStep();
//                $previousStep->created_at = 'nothing submited yet';
//                dd($previousStep->created_at);
            }else{
                $previousStep = ApplicationStep::findOrFail($currentStep->previous_step);
            }


//            dd($currentStep);
//dd($previousStep->name);
            return view('homes.application', [
                'previous_step'         => $previousStep,
                'pageInfo'              => $this->pageInfo,
                'application'           => $application,
                'stepsWithForm'         => $workflowInfo['stepsWithForm'],
                'approvalWithReport'    => $workflowInfo['approvalWithReport'],
                'currentStep'           => $currentStep,
                'isResponsible'         => $isResponsible,
                'errorsFormsFields'     => $errorsFormsFields,
                'cComplianceForms'      => $cComplianceForms,
                'userResponsible'       => $userResponsible,
                'userTypeResponsible'   => $userTypeResponsible
            ]);
        }
    }

    public static function _getApplicationWorkflowInfo($application)
    {
        return self::getApplicationWorkflowInfo($application);
    }

    private static function getApplicationWorkflowInfo($application)
    {
        $stepsWithForm =  $application->steps->where("morphs_from", FormTemplate::class )->all();

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

        $res = [
            'approvalWithReport'    => $reports,
            'stepsWithForm'         => $stepsWithForm

        ];
//    dd($res);
        return $res;
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
