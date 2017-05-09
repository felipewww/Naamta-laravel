<?php

namespace App\Http\Controllers;

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

    /**
     * Convert a form to json.
     *
     * @return response of saved itens
     */
    protected function _convertFormToJson($form){

        $_return = array();
        foreach ($form->containers as $i => $c){
            $_return[$i]["config"] = [
                'id'    =>  $c->id,
                'title'  =>  $c->name,
                'tabId' =>  $c->id,
            ];

            foreach($c->fields as $k => $v){
                $_return[$i]["fields"][$k]["id"] =  $v->id;
                $_return[$i]["fields"][$k]["type"] =  $v->type;
                $_return[$i]["fields"][$k]["container_id"] =  $v->container_id;
                $_return[$i]["fields"][$k]["isEditable"] =  true;
                $_return[$i]["fields"][$k]["options"] =  json_decode($v->config);
                $_return[$i]["fields"][$k]["commments"] =  json_decode("[{username : 'John',msg : 'A Comment'},{username : 'Josephine', msg : 'Another Comment'}]");
            }
        }
        return json_encode($_return);
    }
}
