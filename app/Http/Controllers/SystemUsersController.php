<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

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
    
    public function edit($userId){
        return('Edit User: ' . $userId);
    }
    
    public function delete($userId){
        return('Delete User: ' . $userId);
    }
}
