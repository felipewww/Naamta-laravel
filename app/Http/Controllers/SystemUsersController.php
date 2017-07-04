<?php

namespace App\Http\Controllers;

use App\Library\DataTablesExtensions;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SystemUsersController extends Controller
{
    use DataTablesExtensions;
    private $users;
    private $roles;

    public function __construct()
    {
        parent::__construct();
//        $this->middleware(function ($request, $next) {
//            dd($request);
//            $user = \Auth::user()->authorizeRoles(['admin']);;
//            return $next($request);
//        });
        
        $this->roles = Roles::whereIn('name', ['admin','staff'])->get();
        $this->users = User::whereHas('roles', function($query) {
            $query->whereNotIn('name', ['client']);
        })->get();   
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $this->pageInfo->title              = 'System';
        $this->pageInfo->category->title    = 'Users';
        $this->pageInfo->subCategory->title = 'Users List';

        if (!Auth::user()->hasRole('admin')) {
            return redirect('/');
        }

        $this->dataTablesInit();
        return view('systemUsers.list', ['dataTables' => $this->dataTables, 'pageInfo' => $this->pageInfo ]);
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
                                'html' => '',
                                'attributes' => ['class' => 'btn btn-warning btn-circle fa fa-pencil m-l-10', 'href' => '/users/'.$reg->id.'/edit']
                            ],
                            [
                                'html' => '',
                                'attributes' => ['class' => 'btn btn-danger btn-circle fa fa-trash m-l-10 modal-delete', 'data-toggle'=>'modal', 'data-target' => '#modalDelete', 'data-action' => route('users.destroy' , $reg->id)]
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
            ['title' => 'See all applications'],
            ['title' => 'Status'],
            ['title' => 'Actions'],
        ];
    }
    
    public function create(Request $request){

        $this->pageInfo->title              = 'System';
        $this->pageInfo->category->title    = 'Users';
        $this->pageInfo->subCategory->title = 'Users List';

        return view('systemUsers.form')->with(['roles', $this->roles, 'pageInfo' => $this->pageInfo]);
    }
    
    public function store(Request $request){
        \Session::flash('success_msg', 'User Added.');
        return view('systemUsers.form');
    }
    
    public function edit(Request $request, $id){

        $this->pageInfo->title              = 'System';
        $this->pageInfo->category->title    = 'Users';
        $this->pageInfo->subCategory->title = 'User Edit';

        $user = User::findOrFail($id);

        if($user->roles->first()->name == 'client'){
            if (Auth::user()->id != $user->id) {
                return redirect('/');
            }
        }
        
        return view('systemUsers.form')->with(['user' => $user, 'roles'=>$this->roles, 'pageInfo' => $this->pageInfo]);
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

    public function syncUsers(){
        $url = 'http://localhost.naamta.dev/users.json';
        if($this->get_http_response_code($url) == "200"){
            $str = \GuzzleHttp\json_decode(file_get_contents($url),true);
            $users = $str["users"];
            $role_none = Role::where('name', 'none')->first();

            foreach ($users as $user) {
                if(User::where("email", $user["email"])->count()===0){
                    $staff = new User();
                    $staff->name = $user["name"];
                    $staff->email = $user["email"];
                    $staff->status = 0;
                    $staff->see_apps = 0;
                    $staff->password = bcrypt(Hash::make(str_random(8)));
                    $staff->verified = true;
                    $staff->save();
                    $staff->roles()->attach($role_none);
                }
            }
        }
    }
    public function destroy(Request $request, $userId){
          
        // delete
        try{
            User::where('id', $userId)->delete();
            \Session::flash('message', 'User deleted!');
        }catch (Exception $e){
            \Session::flash('message', 'User delete failed!');
        }

        return redirect('users');
    }
}
