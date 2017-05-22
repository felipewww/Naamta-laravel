<?php

namespace App\Http\Controllers;

use App\MModels\Form;
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
            \Auth::user()->authorizeRoles(['admin', 'staff', 'client']);;
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
                    $form = Form::with(array('containers', 'containers.config', 'containers.fields', 'containers.fields.comments',
                        'containers.fields.setting', 'containers.fields.setting.rule', 'containers.fields.setting.rule.conditions') )->findOrFail($step->morphs_id);
                    return $this->applicationForm($step->id, json_encode($form));
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

    public function saveStepForm(Request $request){
        try{
            $step = ApplicationStep::findOrFail($request->id);
            $step->morphs_json = $request->form_json;
            $step->status = "approved";
            $step->save();

            $nexStep = ApplicationStep::findOrFail( $step->nextStep()->id);
            $nexStep->status = "current";
            $nexStep->save();

            return json_encode(['status' => 'success', 'message' => 'Form saved']);
        }catch (Exception $e){
            return json_encode(['status' => 'error', 'message' => 'Error']);
        }
    }

    public function saveApproval(Request $request){
        try{

            $step = ApplicationStep::findOrFail($request->id);
            $step->morphs_json = $request->form_json;
            $step->status = $request->status;
            $step->save();

            switch ($request->status){
                case 'reproved':
                    $nexStep = ApplicationStep::findOrFail( $step->previousStep()->id);
                    $nexStep->status = "current";
                    $nexStep->save();
                    break;
                default:
                    $nexStep = ApplicationStep::findOrFail( $step->nextStep()->id);
                    $nexStep->status = "current";
                    $nexStep->save();
                    break;
            }

            return json_encode(['status' => 'success', 'message' => 'Form saved']);
        }catch (Exception $e){
            return json_encode(['status' => 'error', 'message' => 'Error']);
        }
    }
}
