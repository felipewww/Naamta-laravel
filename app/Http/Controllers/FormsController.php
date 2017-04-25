<?php

namespace App\Http\Controllers;


use App\Library\DataTablesExtensions;

use App\Http\Controllers\Controller;

use Validator;
use Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;

use App\Models\FormTemplate;
use App\Models\Container;
use App\Models\Field;

class FormsController extends Controller
{
    use DataTablesExtensions;
    private $forms;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->forms = FormTemplate::all();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user()->authorizeRoles(['admin', 'staff']);
        $this->dataTablesInit();
        return view('forms.list', ['dataTables' => $this->dataTables]);
    }

    public function dataTablesConfig()
    {
        $data = [];
        foreach ($this->forms as $reg)
        {
            $newInfo = [
                $reg['id'],
                $reg['name'],
                ($reg['status']) ? 'Active' : 'Inactive',
                [
                    'rowActions' =>
                        [
                            [
                                'html' => 'edit',
                            ],
                            [
                                'html' => 'delete',
                            ]
                        ]
                ]
            ];

            array_push($data, $newInfo);
        }

        $this->data_info = $data;
        $this->data_cols = [
            ['title' => 'id', 'width' => '40px'],
            ['title' => 'Name'],
            ['title' => 'Status'],
            ['title' => 'Actions'],
        ];
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        // get all the nerds
        // load the view and pass the nerds
        return view('forms.form');
    }

   /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        \DB::beginTransaction();
        try{
            $form = FormTemplate::create([
                'name'      => $request->name,
                'status'    => (int)$request->status
            ]);
            $containers = $this->_saveContainers($request->containers, $form->id);
            $fields = $this->_saveFields($containers);

            \Session::flash('success','Form template created: ' . $request->title);
            \DB::commit();
        } catch(Exception $e){
            \Session::flash('error','Form create failed: ' . $e);
            \DB::rollBack();
            throw $e;
        }

        return Redirect::to('forms');
    }

     /**
     * Store a newly created containers.
     *
     * @return containers with id
     */
    private function _saveContainers($_requestContainers, $formId){
        $containers = array();
        $fields = array();
        foreach($_requestContainers as $k => $_arrC){
            $container = new Container();
            $container->form_template_id = $formId;
            foreach($_arrC as $key => $value){
                switch($key){
                    case "config":
                        $container->config = $value;
                    break;
                    case "container":
                        $container->name = "Container " . $value;
                    break;
                    default:
                        $field = new Field([
                            'type' => 1,
                            'config' => $value,
                            'status' => 1
                        ]);
                        $container->fields[] = $field;
                    break;
                }
            }
            $container->save();
            array_push($containers, $container);
        }
        return $containers;
    }


    /**
     * Store a newly created fields.
     *
     * @return response of saved itens
     */
    private function _saveFields($containers){
        $fields = array();
        foreach($containers as $k => $_arrC){
            foreach($_arrC->fields as $key => $field){
                $field->container_id = $_arrC->id;
                array_push($fields, $field->toArray());
            }
        }

        return Field::insert($fields);
    }
}
