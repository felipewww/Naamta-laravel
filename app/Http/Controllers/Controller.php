<?php

namespace App\Http\Controllers;

use App\Models\Approval;
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
use Mockery\CountValidator\Exception;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use PageInfo {
        PageInfo::__construct as public __pageinfo;
    }
    //public $pageInfo;

    public function __construct()
    {
        $this->pageInfo = $this->__pageinfo();
    }

    /*
     * Convert a form to json.
     *
     * @return response of saved itens
     */
    protected function _convertFormToJson($form, $clone = false){
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

        return json_encode($_return);
    }
    
    protected static function _storeApprovalToMongo($approval)
    {
        $mApproval = \App\MModels\Approval::create(['title' => $approval->title, 'description' => $approval->description]);
        $mApproval->save();
        
        return $mApproval->_id;
    }
    
    protected function _storeFormToMongo($form){
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

                if(isset($config->checked))
                    $setting->checked = $config->checked;

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

                    if(isset($v->comments) && count($v->comments) > 0){
                        foreach ($v->comments as $comment) {
                            if(!isset($comment->_id)){
                                $comment = new Comment(["username" => $comment->user_name, "msg" => $comment->text]);
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
        try{
            $field = Field::find($v->_id);
            if(isset($v->setting->value)){
                $field->setting->value = $v->setting->value;
                $field->setting->save();
            }
            if(isset($v->setting->options)){
                $field->setting->options = $v->setting->options;
                $field->setting->save();
            }

            if(isset($v->comments) && count($v->comments) > 0){
                foreach ($v->comments as $comment) {
                    if(!isset($comment->_id)){
                        $comment = new Comment(["username" => $comment->user_name, "msg" => $comment->text]);
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
            if(!isset($item->comment)){
                $comment = new Comment(["username" => $item->comment->user_name, "msg" => $item->comment->text]);
                $field->comments()->save($comment);
            }
            return true;
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
