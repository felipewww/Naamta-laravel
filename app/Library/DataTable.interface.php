<?php

namespace App\Library;

use Illuminate\Http\Request;

interface DataTablesInterface {

    /*
     * O construtor passa a ser esse. sempre após o construtor da trait
     * */
//    function constructorAfterDT(Request $request);

    /*
     * Inherited from DataTablesExtensions
     * */
    function dataTablesInit();

    /*
     * A classe que implementa datatables, deve ter este método
     * */
    function dataTablesConfig();
}