<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Roles;

class SystemUsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        
        $request->user()->authorizeRoles(['admin']);
        
        $users = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'staff']);
        })->get();       
        return view('systemUsers.show')->with(['users'=>$users]);
    }
    
    public function create(Request $request){
        
        $request->user()->authorizeRoles(['admin']);   
        $roles = Roles::whereIn('name', ['admin','staff'])->get();
        return view('systemUsers.edit')->with('roles', $roles);
    }
    
    public function create_post(Request $request){
        
        $request->user()->authorizeRoles(['admin']);
            
        \Session::flash('success_msg','User Added.');
        return view('systemUsers.edit');
    }
    
    public function edit(Request $request, $userId){
        
        $request->user()->authorizeRoles(['admin']);
        
        $user = User::findOrFail($userId);
        $roles = Roles::whereIn('name', ['admin','staff'])->get();
        
        if($user->roles->first()->name == 'client'){
            return redirect('users');
        }
        
        return view('systemUsers.edit')->with(['user'=>$user, 'roles'=>$roles]);
    }
    
    public function edit_post(Request $request, $userId){
        
        
        $request->user()->authorizeRoles(['admin']);
        
        $user = User::findOrFail($userId);
        $user->status = $request->status;
        
        $user->roles()->sync($request->user_type);
        
        //Check if user saved
        
        if ( !$user->save()){
            \Session::flash('error_msg','User Edit Fail.');
        }else{
            \Session::flash('success_msg','User Edited.');
        }
        
        
        return redirect('users/'.$userId.'/edit');
    }
    
    public function delete(Request $request, $userId){
          
        $request->user()->authorizeRoles(['admin']);
        
        $user = User::findOrFail($userId);
        $user->delete();
        
        return redirect('users');
    }
}
