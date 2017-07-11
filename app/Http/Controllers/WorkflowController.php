<?php

namespace App\Http\Controllers;

use App\MModels\Config;
use App\MModels\Form;
use App\Models\ApplicationStepForms;
use App\Models\ApplicationUserTypes;
use App\Models\EmailTemplate;
use App\Models\Field;
use App\Models\Step;
use App\Models\User;
use App\Models\UserApplication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ApplicationStep;
use App\Models\FormTemplate;
use App\Models\Approval;
use App\Models\Report;

class WorkflowController extends Controller
{
    public $step;
    public $application;
    public $allFormsWithErrors = [];
    public $defaultVars;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->defaultVars = new \stdClass();

        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $user->authorizeRoles(['admin', 'staff', 'client']);;

            if($request->id!==null) {
                $this->step = ApplicationStep::findOrFail($request->id);
                $this->application = $this->step->application;
                $userVerify = UserApplication::where('application_id', $this->step->application->id)
                    ->where('user_id', $user->id)
                    ->get();
                if ($userVerify->isEmpty()) {
                    return redirect('/');
                }

                if( $user->hasRole('client') && ( $this->application->status != '1' && $this->application->status != 'completed') ){
                    return redirect('/');
                }
            }
            return $next($request);
        });
    }

    /*
     * **********************************************************
     * Remember, this function requires "artisan queue:listen"
     * **********************************************************
     */
    public function stepActions(Request $request)
    {
        \DB::beginTransaction();

        /*
         * Fall here when submit all forms, so, convert status to approved and find the step
         * */
        if (!$this->step){
            $request->offsetSet('status', 'approved');
            $this->step = ApplicationStep::findOrFail($request->step_id);
            $this->application = $this->step->application;
        }

        $isResponsible = $this->step->loggedUserIsStepResponsible();

        if ( !$isResponsible ) {
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

        switch ($this->step->morphs_from)
        {
            case FormTemplate::class:
                $res = $this->saveStepForm($request);
            break;
            case Approval::class:
                $res = $this->saveApproval($request);
            break;
            default:
                return json_encode(['status' => false]);
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
                    'application_id'     => $this->application->id,
                    'title'              => $emailTemplate->title,
                    'text'               => $contentComplement.$emailTemplate->text,
                    'allFormsWithErrors' => $this->allFormsWithErrors
                ];

                $c = new Carbon();
                $delay = $c->now()->addMinutes(1);
                $job = (new \App\Jobs\WorkflowEmails($request, $mailData, $user))->delay($delay);

                dispatch($job);
            }
        }
//        dd('do not commit');
        \DB::commit();
        return $res;
    }

    public function showReport(Request $request, $id, $report = null)
    {
        $step = ApplicationStep::findOrFail($id);
        $report = $step->approval->report()->findOrFail($report);

        return $this->getApprovalView($id, $step->approval, $report, ['editable' => false]);
    }

    public function show(Request $request, $id, $formId = null)
    {
        $step = ApplicationStep::findOrFail($id);

        switch ($step->morphs_from)
        {
            case FormTemplate::class:
                $form = Form::with(array('containers', 'containers.config', 'containers.fields', 'containers.fields.comments',
                    'containers.fields.setting', 'containers.fields.setting.rule', 'containers.fields.setting.rule.conditions') )->findOrFail($formId);
                $this->step = $step;
                return $this->applicationForm($step->id, $step->responsible, json_encode($form->containers));
                break;

                case Approval::class:
                $this->step = $step;
                return $this->applicationApproval($step->id, $step->responsible, $step->Approval);
                break;

            default:
                throw new \Error('Morph item not found in both table, even on trash. Contact the system administrator');
                break;
        }
    }

    public function showFormErrors(Request $request, $id, $formId = null)
    {
        $form = Form::with(array('containers', 'containers.config', 'containers.fields', 'containers.fields.comments',
            'containers.fields.setting', 'containers.fields.setting.rule', 'containers.fields.setting.rule.conditions') )->findOrFail($formId);

        $appStepFrom = ApplicationStepForms::where('mform_id', $formId)->first();

        return $this->applicationForm($appStepFrom->Step->id, $appStepFrom->Step->responsbile, json_encode($form->containers));
    }

    private function applicationForm($stepId, $stepResponsible, $form)
    {
        $currentStep = ApplicationStep::where("id", $stepId)->first();
        $this->pageInfo->title              = 'Workflow';
        $this->pageInfo->category->title    = $this->pageInfo->application->client->company . "'s ";
        $this->pageInfo->category->link     = "/home";
        $this->pageInfo->subCategory->title = $currentStep->title;


        $activeStep = $currentStep->application->steps()->where('status','current')->first();

        if (!$activeStep) {
            $activeStep = $currentStep->application->steps()->where('status','1')->first();
        }

        $allowEditForm = ( !$activeStep || $currentStep->id != $activeStep->id ) ? false : true;

        return view('workflow.form')->with([
            'stepId' => $stepId,
            'appID' => $currentStep->application_id,
            'isResponsible' => $currentStep->loggedUserIsResponsible(),
            'containers' => $form,
            'pageInfo' => $this->pageInfo,
            'allowEditForm' => $allowEditForm,
            'defaultVars' => $this->defaultVars
        ]);
    }

    public function applicationApproval($stepId, $stepResponsible, $approval)
    {
        $this->pageInfo->title              = 'Workflow';
        $this->pageInfo->category->title    = 'Approval';
        $this->pageInfo->subCategory->title = 'View';

        return $this->getApprovalView($stepId, $approval);
    }

    private function getApprovalView($stepId, $approval, $report = null, $params = [])
    {
        $currentStep = ApplicationStep::where("id", $stepId)->first();

        //Do not allow even admin to edit approval when it isn't editable
        $editable  =( isset($params['editable']) ) ? $params['editable'] : true;
        $isResponsible = ($editable) ? $this->step->loggedUserIsResponsible() : false;

        return
            view('workflow.approval')->with([
                'step' => $this->step,
                'stepId' => $stepId,
                'appID' => $currentStep->application_id,
                'isResponsible' => $isResponsible,
                'approval' => $approval,
                'report' => $report,
                'pageInfo' => $this->pageInfo
            ]);
    }

    public function saveStepForm(Request $request)
    {
        if ( isset($request->form_json) )
        {
            $formJson = json_decode($request->form_json);

            //Find form by config ID
            $formConfigID = $formJson[0]->config->_id;
            $formMongoID = Config::findOrFail($formConfigID)->container->forms->_id;

            //Validate if exists a form with these ID's (step and mform_id)
            $validate = ApplicationStepForms::where('application_step_id', $request->id)->where('mform_id', $formMongoID)->first();

            //If the relations is ok, validate if the step can be updated (if it's a current step)
            $application = $validate->Step->application;

            $activeStep = $application->steps()->where('status','current')->first();

            if (!$activeStep) {
                $activeStep = $application->steps()->where('status','1')->first();
            }

            if ($activeStep->id != $validate->Step->id) {
                abort(401, 'Action not allowed');
            }

            if ( !$validate->Step->loggedUserIsStepResponsible() ) {
                abort(401, 'Action not allowed');
            }


            try{
                if($this->_updateFormToMongo(\GuzzleHttp\json_decode($request->form_json)))

                return json_encode(['status' => 'success', 'message' => 'Form saved']);
            }catch (\Exception $e){
                return json_encode(['status' => 'error', 'message' => 'Error']);
            }
        }

        return null;
    }

    public function gotoNextStep(Request $request)
    {
        $step = ApplicationStep::findOrFail($request->step_id);
        $step->status = "approved";
        $step->save();
        $this->stepActions($request);
        $this->nextStepOrCompleteApplication($step);

        return json_encode(['status' => 'success', 'message' => 'Form saved']);
    }

    public function saveApproval(Request $request)
    {
        try{
            $step = ApplicationStep::findOrFail($request->id);

            if($step->Approval->has_report===1){

                $this->allFormsWithErrors = $this->_getAllFormsErrorsField($step);

                $slightJson = [];

                foreach ($this->allFormsWithErrors as $form)
                {
                    $f = [
                        'name' => $form->name,
                        'fields' => []
                    ];
                    foreach ($form->fieldsWithError as $field)
                    {
                        $fieldData = [
                            'label' => $field->setting->label,
                            'value' => $field->setting->value,
                            'error' => $field->setting->error,
                        ];

                        array_push($f['fields'], $fieldData);
                    }

                    array_push($slightJson, $f);
                }

                Report::create(
                    [
                        'approval_id' => $step->Approval->id,
                        'application_steps_id' => $step->id,
                        'form' => \GuzzleHttp\json_encode($request->form),
                        'forms_errors' => \GuzzleHttp\json_encode($slightJson),
                        'title' => $step->title
                    ]
                );
            }
            $step->status = $request->status;
            $step->save();

            switch ($request->status){
                case 'reproved':
                    $previousStep = ApplicationStep::findOrFail( $step->previous_step );
                    $previousStep->status = "current";
                    $previousStep->save();
                    break;
                default:
                    $this->nextStepOrCompleteApplication($step);
                    break;
            }
            return json_encode(['status' => 'success', 'message' => 'Form saved']);
        }catch (Exception $e){
            return json_encode(['status' => 'error', 'message' => 'Error']);
        }
    }

    public function nextStepOrCompleteApplication(ApplicationStep $step)
    {
        $nextStep = $step->nextStep();
        if ($nextStep)
        {
            $nextStep->status = "current";
            $nextStep->save();
        }
        else
        {
            $app = $step->application;
            $app->status = 'completed';
            $app->save();
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
            $t = $this->_updateFieldToMongo(\GuzzleHttp\json_decode($request->field));
            return json_encode(['status' => 'success', 'message' => 'Comment added']);
        }catch (Exception $e){
            return json_encode(['status' => 'error', 'message' => 'Error']);
        }
    }

}
