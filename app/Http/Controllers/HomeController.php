<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\ActivationService;
use App\Models\Application;
use App\Models\ClientFirstForm;
use App\Models\FormTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;

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
        $application = Application::find($id);
        if ( $application->status == 'wt_firstform' ) {

            $user = Auth::user();
            $this->pageInfo->title              = $user->name."'s".' Registration';
            $this->pageInfo->category->title    = 'Registration';
            $this->pageInfo->subCategory->title = 'Form';
            $this->vars->userType = '$userType';
            $client = $user->client;
            $form = $client->firstForm;
//            dd($client);
//            dd($form);
            return view('applications.first_form',[
                'application'   => $application,
                'pageInfo'      => $this->pageInfo,
                'withAction'    => true,
                'form'          => $form
            ]);

        }else{
            $stepsWithForm = $application->steps->where("morphs_from", FormTemplate::class )->all();
            return view('homes.application', [
                'pageInfo' => $this->pageInfo,
                'application' => $application,
                'stepsWithForm' => $stepsWithForm
            ]);
        }
    }
}
