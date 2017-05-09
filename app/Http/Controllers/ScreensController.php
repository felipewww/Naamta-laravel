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

use App\Models\Screen;

class ScreensController extends Controller
{
    use DataTablesExtensions;
    private $screens;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->screens = Screen::all();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->pageInfo->title              = 'Screens';
        $this->pageInfo->category->title    = 'Workflow';
        $this->pageInfo->subCategory->title = 'Screens List';

        $request->user()->authorizeRoles(['admin', 'staff']);
        $this->dataTablesInit();
        return view('screens.list', ['dataTables' => $this->dataTables, 'pageInfo' => $this->pageInfo]);
    }

    public function dataTablesConfig()
    {
        //echo route('screens');
        $data = [];
        foreach ($this->screens as $reg)
        {
            $newInfo = [
                $reg['id'],
                $reg['title'],
                $reg['description'],
                [
                    'rowActions' =>
                        [
                            [
                                'html' => '',
                                'attributes' => ['class' => 'btn btn-warning btn-circle fa fa-pencil m-l-10', 'href' => '/screens/'.$reg->id.'/edit']
                            ],
                            [
                                'html' => '',
                                'attributes' => ['class' => 'btn btn-danger btn-circle fa fa-trash m-l-10 modal-delete', 'data-toggle'=>'modal', 'data-target' => '#modalDelete', 'data-action' => route('screens.destroy' , $reg->id)]
                            ]
                        ]
                ]
            ];

            array_push($data, $newInfo);
        }

        $this->data_info = $data;
        $this->data_cols = [
            ['title' => 'id', 'width' => '40px'],
            ['title' => 'Title'],
            ['title' => 'Description'],
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
        $this->pageInfo->title              = 'Screens';
        $this->pageInfo->category->title    = 'Workflow';
        $this->pageInfo->subCategory->title = 'Screen Create';

        // get all the nerds
        // load the view and pass the nerds
        return view('screens.form', ['pageInfo' => $this->pageInfo]);
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
            Screen::create([
                'title'       => $request->title,
                'description' => $request->description
            ]);
            \Session::flash('success','Screen template created: ' . $request->title);
            \DB::commit();
        } catch(Exception $e){
            \Session::flash('error','Screen create failed: ' . $e);
            \DB::rollBack();
            throw $e;
        }

        return Redirect::to('forms');
    }

    public function edit(Request $request, $id){

        $this->pageInfo->title              = 'Screens';
        $this->pageInfo->category->title    = 'Workflow';
        $this->pageInfo->subCategory->title = 'Screens Edit';

        $screen = Screen::findOrFail($id);
       
        return view('screens.form')->with(['screen' => $screen, 'pageInfo' => $this->pageInfo]);
    }
    
    public function update(Request $request, $id){
        try{
            Screen::where("id", $id)->update([
                'title'      => $request->title,
                'description'    => $request->description
            ]);

            \Session::flash('success_msg','Screen Edited: ' . $request->title);
        } catch(Exception $e){
            \Session::flash('error','Screen update failed: ' . $e);
        }
        
        return redirect('screens');
    }

    public function destroy(Request $request, $id){
        // delete
        try{
            Screen::where('id', $id)->delete();
            \Session::flash('message', 'Screen deleted!');
        }catch (Exception $e){
            \Session::flash('message', 'Screen delete failed!');
        }

        return redirect('screens');
    }

}
