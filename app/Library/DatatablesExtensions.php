<?php

/*
 * TODO - O namespace correta é LIB... igual do geonames, ja esta cadastrado no psr-4... mudar isso!
 * */
namespace App\Library;

//use App\Http\Controllers\Controller;
//use Illuminate\Http\Request;

/*
 * Extensão PHP que trabalha em conjunto com /js/client/DataTablesExtensions.JS
 * */

trait DataTablesExtensions {
    
    private $tableButtons = [];
    
    public $dataTables;
    public $data_info;
    public $data_cols;

    public function __construct()
    {
        $this->dataTablesInit();
    }

    public function dataTablesInit()
    {
        $this->dataTablesConfig();
        $this->setData();
    }

    /*
     * Adicionar parametros em array (que será convertido para json) e trabalhar em conjunto com o Js
     * */
    protected function add($element)
    {
        array_push($this->tableButtons, $element);
        return $this;
    }

    protected function setData()
    {
        $this->dataTables = [
            'columns' => json_encode($this->data_cols),
            'info' => json_encode($this->data_info)
        ];
        
//        $this->vars['dataTables_columns']   = json_encode($this->data_cols);
//        $this->vars['dataTables_info']      = json_encode($this->data_info);
    }

}