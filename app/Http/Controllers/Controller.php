<?php

namespace App\Http\Controllers;

use App\Models\FormTemplate;
use App\Models\Screen;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Library\PageInfo;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use PageInfo {
        PageInfo::__construct as public __pageinfo;
    }
    public $pageInfo;

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
                'id'    =>  ($clone ? $i : $c->id),
                'title'  =>  $c->name,
                'tabId' =>  ($clone ? "": $c->id),
            ];

            foreach($c->fields as $k => $v){
                $_return[$i]["fields"][$k]["id"] =  ($clone ? null : $v->id);
                $_return[$i]["fields"][$k]["type"] =  $v->type;
                $_return[$i]["fields"][$k]["container_id"] =  $v->container_id;
                $_return[$i]["fields"][$k]["isEditable"] =  true;
                $_return[$i]["fields"][$k]["comments"] = array();
                if(isset($v->comments) && count($v->comments) > 0){
                    foreach ($v->comments as $comment) {
                        array_push($_return[$i]["fields"][$k]["comments"], array("username" => $comment->user_name, "msg" => $comment->text));
                    }
                }
                $_return[$i]["fields"][$k]["options"] =  json_decode($v->config);
            }
        }
        return json_encode($_return);
    }

    protected function _convertScreenToJson(Screen $screen)
    {
        return json_encode($screen->getAttributes());
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
