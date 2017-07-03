<?php

namespace App\Http\Controllers;


use App\Library\DataTablesExtensions;

use App\Http\Controllers\Controller;

use App\MModels\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Validator;
use Session;
use Illuminate\Support\Str;
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
        parent::__construct();
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
        $this->pageInfo->title              = 'Form Templates';
        $this->pageInfo->category->title    = 'Workflow';
        $this->pageInfo->subCategory->title = 'Forms List';

        $request->user()->authorizeRoles(['admin', 'staff']);
        $this->dataTablesInit();
        return view('forms.list', ['dataTables' => $this->dataTables, 'pageInfo' => $this->pageInfo]);
    }

    public function dataTablesConfig()
    {
        //echo route('forms');
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
                                'html' => '',
                                'attributes' => ['class' => 'btn btn-warning btn-circle fa fa-eye m-l-10', 'href' => '/forms/'.$reg->id]
                            ],
                            [
                                'html' => '',
                                'attributes' => ['class' => 'btn btn-warning btn-circle fa fa-clone m-l-10', 'href' => '/forms/'.$reg->id.'/clone']
                            ],
                            [
                                'html' => '',
                                'attributes' => ['class' => 'btn btn-warning btn-circle fa fa-pencil m-l-10', 'href' => '/forms/'.$reg->id.'/edit']
                            ],
                            [
                                'html' => '',
                                'attributes' => ['class' => 'btn btn-danger btn-circle fa fa-trash m-l-10 modal-delete', 'data-toggle'=>'modal', 'data-target' => '#modalDelete', 'data-action' => route('forms.destroy' , $reg->id)]
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
    public function create(Request $request, $id = null)
    {   $this->pageInfo->title              = 'Form Templates';
        $this->pageInfo->category->title    = 'Workflow';
        $this->pageInfo->subCategory->title = 'Form Create';

        if(isset($id)){
            $this->pageInfo->subCategory->title = 'Form Clone';
            $clone = FormTemplate::with( array( 'containers', 'containers.fields', 'containers.fields.comments') )->findOrFail($id);
            $clone->id = null;
            $clone->name = "";
            $clone->status = 0;
            return view('forms.form')->with(['form' => $clone, 'containers' => $this->_convertFormToJson($clone, true), 'pageInfo' => $this->pageInfo]);
        }

        // get all the nerds
        // load the view and pass the nerds
        return view('forms.form', ['pageInfo' => $this->pageInfo]);
    }

    public function mongo(Request $request, $id = null)
    {
        if(isset($id)){
            $form = FormTemplate::with( array( 'containers', 'containers.fields', 'containers.fields.comments') )->findOrFail($id);
            var_dump($this->_storeFormToMongo($form));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        \DB::beginTransaction();
        try{
//            dd($request->all());
            $form = FormTemplate::create([
                'name'      => $request->name,
                'status'    => (int)$request->status
            ]);

            $this->_saveContainers(json_decode($request->containers), $form->id);
            //$fields = $this->_saveFields($containers);

            \Session::flash('success','Form template created: ' . $request->name);
            \DB::commit();
        } catch(Exception $e){
            \Session::flash('error','Form create failed: ' . $e);
            \DB::rollBack();
            throw $e;
        }

        return Redirect::to('forms');
    }

    public function show(Request $request, $id){

        $this->pageInfo->title              = 'Form Templates';
        $this->pageInfo->category->title    = 'Workflow';
        $this->pageInfo->subCategory->title = 'Form View';

        $form = FormTemplate::withTrashed()->with( array( 'containers', 'containers.fields', 'containers.fields.comments') )->findOrFail($id);
       
        return view('forms.show')->with(['form' => $form, 'containers' => $this->_convertFormToJson($form), 'pageInfo' => $this->pageInfo]);
    }

    public function firstFormEdit(){

        if (!Auth::user()->hasRole('admin')) {
            return \redirect('/');
        }

        $this->pageInfo->title              = 'First Form Edit';
        $this->pageInfo->category->title    = 'Register';
        $this->pageInfo->subCategory->title = 'Form';

        $form = FormTemplate::withTrashed()->with( array( 'containers', 'containers.fields', 'containers.fields.comments') )->findOrFail(1);

        return view('forms.form')->with(['form' => $form, 'containers' => $this->_convertFormToJson($form), 'pageInfo' => $this->pageInfo]);
    }

    public function edit(Request $request, $id){
        $this->pageInfo->title              = 'Form Templates';
        $this->pageInfo->category->title    = 'Workflow';
        $this->pageInfo->subCategory->title = 'Form Edit';

        $form = FormTemplate::with( array( 'containers', 'containers.fields', 'containers.fields.comments') )->findOrFail($id);
       
        return view('forms.form')->with(['form' => $form, 'containers' => $this->_convertFormToJson($form), 'pageInfo' => $this->pageInfo]);
    }
    
    public function update(Request $request, $id){
        try{

           FormTemplate::where("id", $id)->update([
                'name'      => $request->name,
                'status'    => (int)$request->status
            ]);

            $this->_saveContainers(json_decode($request->containers), $id);

            \Session::flash('success_msg','Form Edited: ' . $request->name);
        } catch(Exception $e){
            \Session::flash('error','Form update failed: ' . $e);
        }
        
        return redirect('forms');
    }
    
    public function destroy(Request $request, $id){

        // delete
        try{
            FormTemplate::where('id', $id)->delete();
            \Session::flash('message', 'Form deleted!');
        }catch (Exception $e){
            \Session::flash('message', 'Form delete failed!');
        }

        return redirect('forms');
    }

     /**
     * Store a newly created containers.
     *
     * @return array with id
     */
    public function _saveContainers($_requestContainers, $formId){
        $containers = array();
        $fields = array();
        $_oldContainers = Container::where('form_template_id', $formId)->get()->toArray();
        $_oldContainers = array_column($_oldContainers, 'id');
        $_excludeContainers = array();
        foreach($_requestContainers as $k => $_arrC){
            $key = array_search($_arrC->config->tabId, $_oldContainers);

            if($key!==false){
                array_push($_excludeContainers, $_oldContainers[$key]);
            }

            $container = Container::firstOrNew(array('id' => $_arrC->config->tabId));

            $container->name = $_arrC->config->title;
            $container->form_template_id = $formId;
            $container->config = "";
            $container->save();
            $_oldFields = Field::where('container_id', $container->id)->get()->toArray();
            $_oldFields = array_column($_oldFields, 'id');
            $_excludeFields = array();

            if(isset($_arrC->fields)){
                foreach($_arrC->fields as $key => $value) {
                    $fKey = array_search($value->_id, $_oldFields);

                    if($fKey!==false){
                        array_push($_excludeFields, $_oldFields[$fKey]);
                    }

                    $field = Field::firstOrNew(array('id' => $value->_id));
                    $field->container_id = $container->id;
                    $field->type = $value->type;
                    $field->config = json_encode($value->setting);
                    $field->status = 1;
                    $field->save();
                }
            }
            $fieldsToRemove = array_diff($_oldFields, $_excludeFields);
            foreach($fieldsToRemove as $oF){
                Field::where("id", $oF)->delete();
            }
        }
        $contaniersToRemove = array_diff($_oldContainers, $_excludeContainers);

        foreach($contaniersToRemove as $oC){
            Container::where("id", $oC)->delete();
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

    /**
     * Store a newly created fields.
     *
     * @return response of saved itens
     */
    private function _generateRandomName(){
        $string = Str::random(10);
        if(is_array(Field::where("name", $string)->get())>0){
            return $this->_generateRandomName();
        }
        return $string;
    }

    /*
    Example

    var tabObj1 = {
      config : {
        id : 1959595,
        title: 'Title'
      },
      fields : [
        {
          id : 1233123,
          type : 'checkbox-group',
          isEditable : true,
          comments : [
            {
              username : 'John',
              msg : 'A Comment'
            },
            {
              username : 'Josephine',
              msg : 'Another Comment'
            }
          ],
          options : {
            isRequired : true,
            label : 'Label',
            help : 'Help Text',
            value : '',
            min : '',
            max : '',
            step : '',
            type : '',
            options : [
              {
                label : 'Option Label 1',
                value : 'Option Value 1'
              },
              {
                label : 'Option Label 2',
                value : 'Option Value 2'
              }
            ]
          }
        }
      ]
    }*/
}
