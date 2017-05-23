<?php

namespace App\Http\Controllers;

use App\Mail\WorkflowEmails;
use App\MModels\Form;
use App\Models\Application;
use App\Models\ApplicationUserTypes;
use App\Models\ApplicationUsesEmail;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\UserApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\ApplicationStep;
use App\Models\FormTemplate;
use App\Models\Approval;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

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
                    $contentComplement = 'This e-mail should have been sent to: '.$uType->title.', User: '.$user->name.' | '.$user->email.'<br>';

                    $user = new User([
                        'name'  => 'Local Temp User',
                        'email' => env('MAIL_LOCAL_RECEIVER')
                    ]);
                }

                Mail::to($user)->queue(
                    new WorkflowEmails($request->status, [
                        'title' => $emailTemplate->title,
                        'text' => $contentComplement.$emailTemplate->text
                    ])
//                Mail::to($user)->send(
//                    new WorkflowEmails($request->status, [
//                        'title' => $emailTemplate->title,
//                        'text' => $contentComplement.$emailTemplate->text
//                    ])
                );
            }
        }

        return json_encode(['status' => true]);
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
