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

    public function applicationDashboard(Request $request, $id){
        $application = Application::with(['steps'])->find($id);
        if ( $application->status == 'wt_firstform' ) {
            $this->pageInfo->title              = Auth::user()->name."'s".' Registration';
            $this->pageInfo->category->title    = 'Registration';
            $this->pageInfo->subCategory->title = 'Form';
            $this->vars->userType = '$userType';

            return view('applications.first_form',[
                'application'   => $application,
                'pageInfo'      => $this->pageInfo,
                'withAction'    => true,
            ]);

        }else{
            $stepsWithForm = $application->steps->where("morphs_from", FormTemplate::class )->all();
            $approvalWithReport = $application->steps->where('morphs_from', Approval::class)->where('approval.has_report', '1')->all();
            $reports = array();
            foreach($approvalWithReport as $approval){
                $step = ApplicationStep::findOrFail($approval->id);
                if($step->Approval->report!=null){
                    array_push($reports, array('stepId' => $approval->id, 'report' => $step->Approval->report));
                }
            }
            return view('homes.application', [
                'pageInfo' => $this->pageInfo,
                'application' => $application,
                'stepsWithForm' => $stepsWithForm,
                'approvalWithReport' => $reports
            ]);
        }
    }
}
