<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Library\DataTablesExtensions;
use Validator;
use Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Models\UserType;

class UserTypesController extends Controller
{
    use DataTablesExtensions;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $userTypes;
    
    private $rules = [
            'title' => 'required|min:3|max:255'];
    
    public function __construct(Request $request)
    {
        $this->middleware(function ($request, $next) {
            $user = \Auth::user()->authorizeRoles(['admin']);;
            return $next($request);
        });
        $this->userTypes = UserType::all();
    }
    
     /**
     * Show the application UserType list.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $this->dataTablesInit();
        return view('userTypes.list', ['dataTables' => $this->dataTables ]);
    }

    public function dataTablesConfig()
    {
        $data = [];
        foreach ($this->userTypes as $reg)
        {
            $newInfo = [
                $reg['id'],
                $reg['title'],
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
            ['title' => 'Title'],
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
        // load the view and pass the email
        return view('userTypes.form');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request){
        
        Validator::make($request->all(), $this->rules)->validate();
        
         try{
            // store

            $userTypes = UserType::create([
                'slug'  => str_slug($request->title, '-'),
                'title' => $request->title,
                'status'=> (int)$request->status,
            ]);
        
            \Session::flash('success','User Type created: ' . $request->name);

        } catch(Exception $e){

            \Session::flash('error','Email create failed: ' . $e);

        }

        return Redirect::to('usertypes');
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit($id){
        
        $userType = UserType::findOrFail($id);
        
        return view('userTypes.form')->with(['userType' => $userType]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, $id){

        try{

            $userTypes = UserType::where('id', $id)->update([
                'slug'  => str_slug($request->title, '-'),
                'title' => $request->title,
                'status'=> (int)$request->status,
            ]);

            \Session::flash('success','User Type updated: ' . $request->name);

        }catch(Exception $e){
            \Session::flash('error','User Type updated failed: ' . $e);
        }
                
        return Redirect::to('usertypes');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id){
        
        try{
            $userType = UserType::where('id', $id)->delete();
            Session::flash('message', 'User Type deleted!');
            
        }catch (Exception $e){
            Session::flash('message', 'User Type delete failed!');
        }

        return Redirect::to('usertypes');
    }
}