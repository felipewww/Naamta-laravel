<?php

namespace App\Http\Controllers;

use App\Models\Application;
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
        $userType = Auth::user()->roles[0]->name;

        $this->pageInfo->title              = Auth::user()->name."'s".' Dashboard';
        $this->pageInfo->category->title    = $userType;
        $this->pageInfo->subCategory->title = 'Homepage';
        $this->vars->userType = $userType;

        if($userType==="client"){
            $application = Client::where("user_id", Auth::id())->first()->application()->first();
            if(isset($application)){
                if($application->status===0){
                    return view('homes.wait_approval', ['pageInfo' => $this->pageInfo]);
                }
                return $this->applicationDashboard($request, $application->id);
            }
        }
        $this->vars->activeApplications = Application::where('status', 1)->get();
        $this->vars->inactiveApplications = Application::where('status', 0)->get();

        return view('homes.admin', ['vars' => $this->vars, 'pageInfo' => $this->pageInfo]);
    }

    public function applicationDashboard(Request $request, $id){
        $application = Application::find($id);
        $stepsWithForm = $application->steps->where("morphs_from", FormTemplate::class )->all();
        return view('homes.application', ['pageInfo' => $this->pageInfo, 'application' => $application, 'stepsWithForm' => $stepsWithForm]);
    }
}
