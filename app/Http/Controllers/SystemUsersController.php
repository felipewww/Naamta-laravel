<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Roles;

class SystemUsersController extends Controller
{
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
        return view('systemUsers.list')->with(['users' => $this->users]);
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
