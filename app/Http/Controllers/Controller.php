<?php

namespace App\Http\Controllers;

use App\Models\ApplicationStep;
use App\Models\ApplicationStepForms;
use App\Models\Approval;
use App\Models\FormTemplate;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Library\PageInfo;
use \App\MModels\Form;
use \App\MModels\Config;
use \App\MModels\Container;
use \App\MModels\Field;
use \App\MModels\Condition;
use \App\MModels\Comment;
use \App\MModels\Setting;
use \App\MModels\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Mockery\CountValidator\Exception;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use PageInfo {
        PageInfo::__construct as public __pageinfo;
    }

    public function __construct()
    {
        $this->pageInfo = $this->__pageinfo();
    }

    public function storeFiles($folder, $files, $data = [])
    {
        $storage = Storage::disk('public');
        
        $exists = $storage->exists($folder);

        if ( !$exists ) {
            $storage->makeDirectory($folder);
        }

        foreach ($files as $inputName => $file)
        {
            $path = $storage->put($folder, $file);

            $data[$inputName] = $path;
        }

        return $data;
    }

//    public function isStepResponsible($step)
//    {
//        $currentUserType = $step->application->users()->where('user_id', Auth::user()->id)->get();
//
//        if (!$currentUserType) {
//            abort(401, 'Unauthorized.');
//        }
//
//
//        if ($currentUserType->count() == 1)
//        {
//            $currentUserType = $currentUserType->first();
//            $isResponsible = ($step->responsible == $currentUserType->user_type);
//        }
//        else
//        {
//            $isResponsible = $currentUserType->where('user_type', $step->responsible)->first();
//        }
//
//        return $isResponsible;
//    }

    public function urlStoredFile($path)
    {
        $storage = Storage::disk('public');
        $url = $storage->url($path);
        
        return $url;
    }

    function uploadFiles(Request $request) {
        $folder = $request->folder;
        return $this->storeFiles($folder, $request->allFiles(), []);
    }

    /*
     * Convert a form to json.
     *
     * @return response of saved items
     */
    public function _convertFormToJson($form, $clone = false){
        $_return = array();
        foreach ($form->containers as $i => $c){
            $_return[$i]["config"] = [
                '_id'    =>  ($clone ? $i : $c->id),
                'title'  =>  $c->name,
                'tabId' =>  ($clone ? "": $c->id),
            ];

            foreach($c->fields as $k => $v){
                $_return[$i]["fields"][$k]["_id"] =  ($clone ? null : $v->id);
                $_return[$i]["fields"][$k]["type"] =  $v->type;
                $_return[$i]["fields"][$k]["container_id"] =  $v->container_id;
                $_return[$i]["fields"][$k]["isEditable"] =  true;
                $_return[$i]["fields"][$k]["comments"] = array();
                if(isset($v->comments) && count($v->comments) > 0){
                    foreach ($v->comments as $comment) {
                        array_push($_return[$i]["fields"][$k]["comments"], array("username" => $comment->user_name, "msg" => $comment->text));
                    }
                }
                $_return[$i]["fields"][$k]["setting"] =  json_decode($v->config);
            }
        }

//        dd($_return);

//        | JSON_PRETTY_PRINT
//        JSON_HEX_TAG |
//        JSON_HEX_QUOT
//        |  JSON_HEX_AMP
        return json_encode($_return);
//        return json_encode($_return, JSON_HEX_APOS | JSON_NUMERIC_CHECK  | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);
    }
    
    protected static function _storeApprovalToMongo($approval)
    {
        $mApproval = \App\MModels\Approval::create(['title' => $approval->title, 'description' => $approval->description]);
        $mApproval->save();
        
        return $mApproval->_id;
    }
    
    public function _storeFormToMongo($form){
        $mForm = Form::create(['name' => $form->name, 'status' => $form->status]);

        foreach ($form->containers as $i => $c){
            $container = new Container([]);
            $mForm->containers()->save($container);
            foreach($c->fields as $k => $v){

                $field = new Field();
                $container->fields()->save($field);
                $field->type = $v->type;
                $field->isEditable = true;

                if(isset($v->comments) && count($v->comments) > 0){
                    foreach ($v->comments as $comment) {
                        $comment = new Comment(["username" => $comment->user_name, "msg" => $comment->text]);
                        $field->comments()->save($comment);
                    }
                }

                $config =  \GuzzleHttp\json_decode($v->config);

                $setting = new Setting();
                $field->setting()->save($setting);

                if(isset($config->ordenate))
                    $setting->ordenate = $config->ordenate;

                if(isset($config->isRequired))
                    $setting->isRequired = $config->isRequired;

                if(isset($config->label))
                    $setting->label = $config->label;

                if(isset($config->help))
                    $setting->help = $config->help;

                if(isset($config->value))
                    $setting->value = $config->value;

                if(isset($config->placeholder))
                    $setting->placeholder = $config->placeholder;

                if(isset($config->checked))
                    $setting->checked = $config->checked;

                if(isset($config->showEvaluation))
                    $setting->showEvaluation = $config->showEvaluation;

                if(isset($config->min))
                    $setting->min = $config->min;

                if(isset($config->max))
                    $setting->max = $config->max;

                if(isset($config->step))
                    $setting->step = $config->step;

                if(isset($config->class))
                    $setting->class  = $config->class;

                if(isset($config->signature))
                    $setting->signature = $config->signature;

                if(isset($config->options))
                    $setting->options = $config->options;

                if(isset($config->rule)){
                    $r = $config->rule;
                    $rule = new Rule();
                    $setting->rule()->save($rule);

                    if(isset($r->ruleAction))
                        $rule->ruleAction = $r->ruleAction;

                    if(isset($r->ruleTarget))
                        $rule->ruleTarget = $r->ruleTarget;

                    if(isset($r->conditions)){
                        foreach($r->conditions as $co) {
                            $condition = new Condition(
                                [
                                    "page" => $co->page,
                                    "field" => $co->field,
                                    "comparison" => $co->comparison,
                                    "value" => $co->value,
                                ]);
                            $rule->conditions()->save($condition);
                        }
                    }
                    $setting->rule()->save($rule);
                }
                $field->setting()->save($setting);
                $container->fields()->save($field);
            }
            $config = new Config(['title' =>  $c->name, 'tabId' => $c->id]);
            $container->config()->save($config);
        }
        return $mForm->_id;
    }

    protected function _updateFormToMongo($containers){

        try{
            foreach ($containers as $i => $c){
                //$container = Container::find($c->_id);
                foreach($c->fields as $k => $v){
                    $field = Field::find($v->_id);
                    if(isset($v->setting->value)){
                        $field->setting->value = $v->setting->value;
                        $field->setting->save();
                    }
                    if(isset($v->setting->options)){
                        $field->setting->options = $v->setting->options;
                        $field->setting->save();
                    }
                    if(isset($v->setting->error)){
                        $field->setting->error = $v->setting->error;
                        $field->setting->save();
                    }else{
                        $field->setting->error = false;
                        $field->setting->save();
                    }

                    if(isset($v->comments) && count($v->comments) > 0){
                        foreach ($v->comments as $comment) {
                            if(!isset($comment->_id)){
                                $comment = new Comment(["username" => $comment->username, "msg" => $comment->msg]);
                                $field->comments()->save($comment);
                            }
                        }
                    }
                    $field->save();
                }
            }
            return true;
        }catch (Exception $e){
            return false;
        }
    }

    protected function _updateFieldToMongo($v){
//        dd($v);
        try{
            $field = Field::find($v->_id);

            if(isset($v->setting->signature)){

                //Admin and staffs not allowed to change values, only client and just when field not passed
                if (Auth::user()->hasRole(['client']) && $field->setting->error != "Pass") {
                    $field->setting->signature = $v->setting->signature;
                    $field->setting->save();
                }else{
                    return false;
                }
            }
            if(isset($v->setting->value)){

                //$fieldJson = json_decode($v->field);

                //Find form by Field ID
                $fieldID = $field->_id;

                $mform = \App\MModels\Field::findOrFail($fieldID)->container->forms;
                $formMongoID = $mform->_id;

                //verification to resolve bugs on upload image from first form
                $appStepForm = ApplicationStepForms::where('mform_id', $formMongoID)->first();
                if($appStepForm!=null){
                    $step = $appStepForm->Step;
                    if ( !$step->loggedUserIsStepResponsible() ) {
                        abort(401, 'Action not allowed');
                    }

                    $application = $step->application;

                    $activeStep = $application->steps()->where('status','current')->first();

                    if (!$activeStep) {
                        $activeStep = $application->steps()->where('status','1')->first();
                    }

                    if ($activeStep->id != $step->id) {
                        abort(401, 'Action not allowed');
                    }
                }

//                if ( !$step->loggedUserIsStepResponsible() ) {
//                    abort(401, 'Action not allowed');
//                }

                $field->setting->value = $v->setting->value;
                $field->setting->save();
                //Admin and staffs not allowed to change values, only client and just when field not passed
//                if (Auth::user()->hasRole(['client']) && $field->setting->error != "Pass") {
//                    $field->setting->value = $v->setting->value;
//                    $field->setting->save();
//                }else{
//                    return false;
//                }
            }
            if(isset($v->setting->options)){
                $field->setting->options = $v->setting->options;
                $field->setting->save();
            }
            if(isset($v->setting->error)){
//                if (Auth::user()->hasRole(['admin','staff'])) {
                if (Auth::user()->authorizeRoles(['admin','staff'])) {
                    $field->setting->error = $v->setting->error;
                    $field->setting->save();
                }
            }else{
                $field->setting->error = false;
                $field->setting->save();
            }
            if(isset($v->comments) && count($v->comments) > 0){
                foreach ($v->comments as $comment) {
                    if(!isset($comment->_id)){
                        $comment = new Comment(["username" => $comment->username, "msg" => $comment->msg]);
                        $field->comments()->save($comment);
                    }
                }
            }
            $field->save();
            return true;

        }catch (Exception $e){
            return false;
        }
    }

    protected function _addCommentToMongo($item){
        try{
            $field = Field::find($item->fieldId);
            if(isset($item)){
                $comment = new Comment(["username" => $item->username, "msg" => $item->msg, "type" => $item->type]);
                $field->comments()->save($comment);
            }
            return $comment->_id;
        }catch (Exception $e){
            return false;
        }
    }
    protected function _convertApprovalToJson(Approval $approval)
    {
        return json_encode($approval->getAttributes());
    }

    protected function _setMultipleSelectItem(&$items, Array $ids)
    {
        $a = [];
        foreach ($items as $key => $item)
        {
            $item->offsetSet('disabled', '');

            if ($item->deleted_at) {
                $item->disabled = 'disabled="disabled"';
            }
            $item->offsetSet('selected', '');

            $valid = in_array($item->id, $ids);
            if ( $valid )
            {
                //Enable deleted item for allow to keep deleted-form related with this step
                if ($item->deleted_at) {
                    $item->disabled = '';
                }

                array_push($a, array_search($item->id, $ids));
                $item->selected = 'selected="selected"';
            }

            if ($item->disabled == 'disabled="disabled"') {
                $items->forget($key);
            }
        }
    }

    protected function _setSelectedItem(&$items, $id)
    {
        foreach ($items as $item)
        {
            $item->offsetSet('selected', '');

            if ( $item->id == $id )
            {
                $item->selected = 'selected="selected"';
            }
        }
    }

    protected function get_http_response_code($url) {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }

    protected function _getAllFormsErrorsField($step)
    {
        $app = $step->application;
        $allSteps = ApplicationStep::where('application_id', $app->id)->where('morphs_from', FormTemplate::class)->get();
        $allStepsIDs = [];
        foreach ($allSteps as $appStep)
        {
            array_push($allStepsIDs, $appStep->id);
        }

        //Find all forms that was been sent in all steps
        $forms = ApplicationStepForms::whereIn('application_step_id', $allStepsIDs)->get();

        $mongoFormsIDS = [];
        foreach ($forms as $submitedForm)
        {
            array_push($mongoFormsIDS, $submitedForm->mform_id);
        }


        $mongoForms = Form::whereIn('_id', $mongoFormsIDS)->with([
            'containers',
            'containers.fields',
            'containers.fields.setting',
        ])->get();

        $formsWithError = [];

        foreach ($mongoForms as &$mForm)
        {
            $errors = $this->_getErrorsField($mForm, 'field');

            if ($errors) {
                $mForm->offsetSet('fieldsWithError', $errors);
                array_push($formsWithError, $mForm);
            }
        }

        //dd($formsWithError);

        return $formsWithError;
    }

    protected function _getLastFormErrorsField($step){
        if($step->morphs_from === FormTemplate::class)
        {
            $errors = array();
            foreach($step->forms as $form){
                $f = Form::with(array('containers', 'containers.config', 'containers.fields', 'containers.fields.comments',
                    'containers.fields.setting', 'containers.fields.setting.rule', 'containers.fields.setting.rule.conditions') )->findOrFail($form->mform_id);

                array_push($errors, array("formId" => $form->form_templates_id, "containers" => $this->_getErrorsField($f)));
            }

            return $errors;
        }
        if( $step->previousStep() !== null){
            return $this->_getLastFormErrorsField($step->previousStep());
        }
    }

    protected function _getErrorsField($form, $wich = 'errors'){
        $errors = array();
        $errorsCount = [
            'Pass'  => 0,
            'Fail'  => 0,
            'Audit' => 0,
        ];

        $arr = [];
        try{
            foreach ($form->containers as $i => $c){
                foreach($c->fields as $k => $v){
                    if(isset($v->setting->error)){
                        if ($v->setting->error == 'Fail' || $v->setting->error == 'Audit') {
                            $errorsCount[$v->setting->error] = $errorsCount[$v->setting->error]+1;
                            array_push($arr, $v);
                            array_push($errors, Field::find($v->_id));
                        }else{
                            $errorsCount['Pass'] = $errorsCount['Pass']+1;
                        }
                    }
                }
            }

            $errors['errorsCount'] = $errorsCount;
            $arr['errorsCount'] = $errorsCount;

            if ($wich == 'errors'){
                return $errors;
            }else{
                return $arr;
            }
        }catch (Exception $e){
            return null;
        }
    }
}

/*
 * [
	{
		"config" : {
			"id" : "0",
			"title" : "Title",
			"tabId" : "0"
		},
		"fields" : [
			{
				"id" : "0",
				"type" : "select",
				"isEditable" : true,
				"settings" : {
					"ordenate" : "1",
					"isRequired" : true,
					"label" : "Label",
					"help" : "Help Text",
					"value" : "Value",
					"checked" : true,
					"min" : "Min Value",
					"max" : "Max Value",
					"step" : "Step Value (for numbers)",
					"class" : "half-row or empty",
					"signature" : "base64",
					"options" : [
						{
							"label" : "Label",
							"value" : "Value",
							"prop" : true,
						}
					],
					"rules" : {
						"ruleAction" : "Hide or Show",
						"ruleTarget" : "Any or All",
						"conditions" : [
							{
								"page" : {
									"id" : "0",
									"label" : "Page Title"
								},
								"field" : {
									"id" : "0",
									"index" : "1",
									"Label" : "Field Label"
								},
								"comparison" : {
									"value" : "==",
									"label" : "Is"
								},
								"value" : {
									"value" : "value",
									"label" : "label"
								}
							}
						]
					}
				},
				"comments" : [
					{
						"username" : "User Name",
						"msg" : "Message",
						"type" : "internal or external"
					}
				]
			}
		]
	}
]
 *
 * */
