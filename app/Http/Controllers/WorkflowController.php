<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\ApplicationStep;
use App\Models\FormTemplate;
use App\Models\Approval;

class WorkflowController extends Controller
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

    public function show(Request $request, $id){
        $step = ApplicationStep::findOrFail($id);
        if ($step->morphs_id)
        {
            switch ($step->morphs_from)
            {
                case FormTemplate::class:
                    return $this->applicationForm($step->id, $step->morphs_json);
                    break;

                case Approval::class:
                    return $this->applicationApproval($step->id, $step->morphs_json);
                    break;

                default:
                    throw new \Error('Morph item not found in both table, even on trash. Contact the system administrator');
                    break;
            }
        }
    }

    private function applicationForm($stepId, $form){
        $this->pageInfo->title              = 'Workflow';
        $this->pageInfo->category->title    = 'Form';
        $this->pageInfo->subCategory->title = 'View';
        return view('workflow.form')->with(['stepId' => $stepId, 'containers' => $form, 'pageInfo' => $this->pageInfo]);
    }

    public function applicationApproval($stepId, $approval){
        $this->pageInfo->title              = 'Workflow';
        $this->pageInfo->category->title    = 'Approval';
        $this->pageInfo->subCategory->title = 'View';
        return view('workflow.approval')->with(['stepId' => $stepId,'approval' => json_decode($approval), 'pageInfo' => $this->pageInfo]);
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
                if($application->status===0){
                    return view('homes.wait_approval', ['pageInfo' => $this->pageInfo]);
                }

                return $this->applicationDashboard($request, $application->id);
            }
        }
        return view('homes.'.$userType, ['pageInfo' => $this->pageInfo]);
    }

    public function applicationDashboard(Request $request, $id){
        $application = Application::find($id);
        return view('homes.application', ['pageInfo' => $this->pageInfo,'application' => $application]);
    }

    public function saveStepForm(Request $request){
        try{
            $step = ApplicationStep::findOrFail($request->id);
            $step->morphs_json = $request->form_json;
            $step->save();
            return json_encode(['status' => 'success', 'message' => 'Form saved']);
        }catch (Exception $e){
            return json_encode(['status' => 'error', 'message' => 'Error']);
        }
    }
}
