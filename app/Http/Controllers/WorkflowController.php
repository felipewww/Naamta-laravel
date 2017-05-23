<?php

namespace App\Http\Controllers;

use App\MModels\Form;
use App\Models\ApplicationUserTypes;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\UserApplication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ApplicationStep;
use App\Models\FormTemplate;
use App\Models\Approval;

class WorkflowController extends Controller
{
    public $step;
    public $application;
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

            $this->step = ApplicationStep::findOrFail($request->id);
            $this->application = $this->step->application;
            $userVerify = UserApplication::where('application_id', $this->step->application->id)
                ->where('user_id', Auth::user()->id)
                ->get();

            if ( $userVerify->isEmpty() ){
                return redirect('/');
            }

            return $next($request);
        });
    }

    /*
     * **********************************************************
     * Remeber, this function requires "artisan queue:listen"
     * **********************************************************
     */
    public function stepActions(Request $request)
    {
        if ( $this->step->responsible != Auth::user()->id ) {
            //dd('voce nao tem permissao de ação neste step');
            return redirect('/');
        }

        switch ($request->status)
        {
            case 'approved';
                $receivedByList = $this->step->usesEmails()->where('send_when','success')->get();
                break;

            case 'reproved';
                $receivedByList = $this->step->usesEmails()->where('send_when','rejected')->get();
                break;

            default:
                $receivedByList = [];
                break;
        }

        foreach ($receivedByList as $receiver)
        {
            $uType          = ApplicationUserTypes::where('application_id', $this->application->id)->where('id', $receiver->received_by)->first();
            $emailTemplate  = EmailTemplate::where('id', $receiver->email_id)->first();
            $usersRelated   = UserApplication::where('user_type', $uType->id)->where('application_id', $this->application->id)->get();

            foreach ($usersRelated as $userApp)
            {
                $user = User::findOrFail($userApp->user_id);

                $contentComplement = '';

                if (app('env') == 'local')
                {
                    $contentComplement = 'This e-mail should have been sent to: '.$uType->title.', User: '.$user->name.' | '.$user->email.'when step is '.$request->status.'<br>';
                }

                $mailData = [
                    'title' => $emailTemplate->title,
                    'text' => $contentComplement.$emailTemplate->text
                ];

                /*
                 * Não delete este comentário!
                 */
                $c = new Carbon();
                $delay = $c->now()->addMinutes(1);
                $job = (new \App\Jobs\WorkflowEmails($request, $mailData, $user))->delay($delay);
//                $job = (new \App\Jobs\WorkflowEmails($request, $mailData, $user));

                dispatch($job);
            }
        }

        switch ($this->step->morphs_from)
        {
            case FormTemplate::class:
                return $this->saveStepForm($request);
                break;

            case Approval::class:
                return $this->saveApproval($request);
                break;

            default:
                return json_encode(['status' => false]);
                break;
        }
    }

    public function show(Request $request, $id, $formId = null){
        $step = ApplicationStep::findOrFail($id);
        switch ($step->morphs_from)
        {
            case FormTemplate::class:
                $itemId = (isset($formId) ? $step->forms()->findOrFail($formId)->mform_id : $step->forms()->first()->mform_id);
                $form = Form::with(array('containers', 'containers.config', 'containers.fields', 'containers.fields.comments',
                    'containers.fields.setting', 'containers.fields.setting.rule', 'containers.fields.setting.rule.conditions') )->findOrFail($itemId);
                return $this->applicationForm($step->id, json_encode($form->containers));
                break;
            case Approval::class:
                return $this->applicationApproval($step->id, $step->morphs_json);
//                return $this->applicationApproval($step->id, $step->morphs_json);
                break;
            default:
                throw new \Error('Morph item not found in both table, even on trash. Contact the system administrator');
                break;
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
            if($this->_updateFormToMongo(\GuzzleHttp\json_decode($request->form_json)))
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
//            $step->morphs_json = $request->form_json;
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

    public function addFieldComment(Request $request){
        try{
            $comment_id = $this->_addCommentToMongo(\GuzzleHttp\json_decode($request->comment));
            return json_encode(['status' => 'success', 'message' => 'Comment added', 'commentId' => $comment_id]);
        }catch (Exception $e){
            return json_encode(['status' => 'error', 'message' => 'Error']);
        }
    }

    public function updateFormField(Request $request){
        try{
            $this->_updateFieldToMongo(\GuzzleHttp\json_decode($request->field));
            return json_encode(['status' => 'success', 'message' => 'Comment added']);
        }catch (Exception $e){
            return json_encode(['status' => 'error', 'message' => 'Error']);
        }
    }

}
