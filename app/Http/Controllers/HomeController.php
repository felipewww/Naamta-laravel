<?php

namespace App\Http\Controllers;

use App\Models\Application;
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
    public function __construct()
    {
        parent::__construct();
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
        $this->pageInfo->title              = 'Homepage';
        $this->pageInfo->category->title    = '';
        $this->pageInfo->subCategory->title = 'Homepage';

        $userType = Auth::user()->roles[0]->name;

        if($userType==="client"){
            $application = Client::where("user_id", Auth::id())->first()->application()->first();
            if(isset($application)){
                if($application->status===0)
                    return view('homes.wait_approval', ['pageInfo' => $this->pageInfo]);

                return $this->applicationDashboard($request, $application->id);
            }
        }
        return view('homes.'.$userType, ['pageInfo' => $this->pageInfo]);
    }

    public function applicationDashboard(Request $request, $id){
        $application = Application::find($id);
        return view('homes.application', ['pageInfo' => $this->pageInfo,'application' => $application]);
    }

}
