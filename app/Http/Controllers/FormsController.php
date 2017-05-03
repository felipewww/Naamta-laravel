<?php

namespace App\Http\Controllers;


use App\Library\DataTablesExtensions;

use App\Http\Controllers\Controller;

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
                                'html' => 'edit',
                                'attributes' => ['href' => '/forms/'.$reg['id'].'/edit']
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
     * Show the form for creating a new resource.
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
           
            $containers = $this->_saveContainers(json_decode($request->containers), $form->id);
            $fields = $this->_saveFields($containers);

            \Session::flash('success','Form template created: ' . $request->name);
            \DB::commit();
        } catch(Exception $e){
            \Session::flash('error','Form create failed: ' . $e);
            \DB::rollBack();
            throw $e;
        }

        return Redirect::to('forms');
    }

    public function edit(Request $request, $id){
        
        $form = FormTemplate::with( array( 'containers', 'containers.fields') )->findOrFail($id);
       
        $this->_convertFormToJson($form);
        
        return view('forms.form')->with(['form' => $form, 'containers' => $this->_convertFormToJson($form)]);
    }
    
    public function update(Request $request, $id){
        try{

           FormTemplate::where("id", $id)->update([
                'name'      => $request->name,
                'status'    => (int)$request->status
            ]);

            $containers = $this->_saveContainers(json_decode($request->containers), $id);
            $fields = $this->_saveFields($containers);

            \Session::flash('success_msg','Form Edited: ' . $request->name);
        } catch(Exception $e){
            \Session::flash('error','Form update failed: ' . $e);
        }
        
        return redirect('forms');
    }
    
    public function delete(Request $request, $id){
          
        // delete
        try{
            FormTemplate::where('id', $id)->delete();
            Session::flash('message', 'Form deleted!');
        }catch (Exception $e){
            Session::flash('message', 'Form delete failed!');
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
            $container = Container::findOrNew($_arrC->id);
            $container->form_template_id = $formId;
            $container->name = "Container " . $k;
            $container->config = "";
            if(isset($_requestContainers->config)){
                $container->config = $_requestContainers->config;
            }
            
            if(isset($_arrC->fields)){
                foreach($_arrC->fields as $key => $value){
                    $field = Field::firstOrNew(array('id' => $value->id));
                    $field->type = $value->type;
                    $field = Field::updateOrCreate([
                        'type' => $value->type,
                        'config' => json_encode($value->options),
                        'status' => 1
                    ]);
                    $container->fields[] = $field;
                }
            }

            array_push($containers, $container);
            $container->save();
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

    // type : 'checkbox-group',
    //   isEditable : true,
    //   options : {
    //     isRequired : true,
    //     label : 'Label',
    //     help : 'Help Text',
    //     value : '',
    //     min : '',
    //     max : '',
    //     step : '',
    //     type : '',
    //     options : [
    //       {
    //         label : 'Option Label',
    //         value : 'Option Value'
    //       }
    //     ]
    //   }
}
