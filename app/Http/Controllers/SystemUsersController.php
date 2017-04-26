<?php

namespace App\Http\Controllers;

use App\Library\DataTablesExtensions;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Roles;

class SystemUsersController extends Controller
{
    use DataTablesExtensions;
    private $users;
    private $roles;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = \Auth::user()->authorizeRoles(['admin']);;
            return $next($request);
        });
        
        $this->roles = Roles::whereIn('name', ['admin','staff'])->get();
        $this->users = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'staff']);
        })->get();   
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $this->dataTablesInit();
        return view('systemUsers.list', ['dataTables' => $this->dataTables ]);
    }

    public function dataTablesConfig()
    {
        $data = [];
        foreach ($this->users as $reg)
        {
            $newInfo = [
                $reg['id'],
                $reg['name'],
                $reg->roles()->first()->name,
                ($reg['see_apps'] == 1) ? 'True' : 'False',
                ($reg['status'] == 1) ? 'Active' : 'Inactive',
                [
                    'rowActions' =>
                        [
                            [
                                'html' => 'fa fa-trash',
                                'attributes' => ['class' => 'btn btn-warning btn-circle']
                            ],
                            [
                                'html' => 'delete',
                                'attributes' => ['class' => 'btn btn-danger btn-circle']
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
            ['title' => 'Role'],
            ['title' => 'Se all Apps'],
            ['title' => 'Status'],
            ['title' => 'Actions'],
        ];
    }
    
    public function create(Request $request){
        return view('systemUsers.form')->with('roles', $this->roles);
    }
    
    public function store(Request $request){
        \Session::flash('success_msg', 'User Added.');
        return view('systemUsers.form');
    }
    
    public function edit(Request $request, $id){
        
        $user = User::findOrFail($id);
        
        if($user->roles->first()->name == 'client'){
            return redirect('users');
        }
        
        return view('systemUsers.form')->with(['user' => $user, 'roles'=>$this->roles]);
    }
    
    public function update(Request $request, $id){
        try{
            $user = User::findOrFail($id);
            $user->status = $request->status;
            $user->see_apps = $request->see_apps;
            $user->roles()->sync($request->user_type);
            $user->save();
            \Session::flash('success_msg','User Edited: ' . $user->name);
        } catch(Exception $e){
            \Session::flash('error','User update failed: ' . $e);
        }
        
        return redirect('users');
    }
    
    public function delete(Request $request, $userId){
          
        // delete
        try{
            User::where('id', $id)->delete();
            Session::flash('message', 'User deleted!');
        }catch (Exception $e){
            Session::flash('message', 'User delete failed!');
        }

        return Redirect::to('users');
    }
}
