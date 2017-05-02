<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Convert a form to json.
     *
     * @return response of saved itens
     */
    protected function _convertFormToJson($form){

        $_return = array();
        foreach ($form->containers as $i => $c){
            $_return[$i]["id"]   = $c->id;
            $_return[$i]["name"] = $c->name;
            $_return[$i]["config"] = $c->config;
            foreach($c->fields as $k => $v){
                $_return[$i]["fields"][$k]["type"] =  $v->type;
                $_return[$i]["fields"][$k]["container_id"] =  $v->container_id;
                $_return[$i]["fields"][$k]["isEditable"] =  true;
                $_return[$i]["fields"][$k]["options"] =  json_decode($v->config);
            }
        }

        return json_encode($_return);
    }
}
