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
        $request->user()->authorizeRoles(['admin', 'staff']);
        $this->dataTablesInit();
        return view('screens.list', ['dataTables' => $this->dataTables]);
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
                                'html' => 'edit',
                                'attributes' => ['href' => '/screens/'.$reg['id'].'/edit']
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
        // get all the nerds
        // load the view and pass the nerds
        return view('screens.form');
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
        
        $screen = Screen::findOrFail($id);
       
        return view('screens.form')->with(['screen' => $screen]);
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
    
    public function delete(Request $request, $id){
          
        // delete
        try{
            Screen::where('id', $id)->delete();
            Session::flash('message', 'Screen deleted!');
        }catch (Exception $e){
            Session::flash('message', 'Screen delete failed!');
        }

        return Redirect::to('screens');
    }

}
