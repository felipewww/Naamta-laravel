<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsersTypes;

class UserTypesController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    //====================================
    
    public function index(Request $request){
        
        $request->user()->authorizeRoles(['admin']);
        
        $usertype = UsersTypes::all();
        
        return view('usertype.show')->with(['usertype'=>$usertype]);
    }
    
    public function insert(Request $request){
        
        $request->user()->authorizeRoles(['admin']);
        
        return view('usertype.edit');
    }
    
    public function insert_post(Request $request){
        
        $request->user()->authorizeRoles(['admin']);
        
        $slug = str_slug($request->name, '-');
        $data = array('slug' => $slug, 'title' => $request->name, 'status'=>$request->status);
        $usertype = UsersTypes::create($data);
        
        if ( !$usertype ){
            \Session::flash('error_msg','Fail at create user type.');
        }else{
            \Session::flash('success_msg','User type created.');
        }
        
        return redirect('userstype/create');
    }
    
    public function update(Request $request, $userId){
        
        $request->user()->authorizeRoles(['admin']);
        
        $usertype = UsersTypes::findOrFail($userId);
        
        return view('usertype.edit')->with(['usertype' => $usertype]);
    }
    
    public function update_post(Request $request, $userId){
        
        $request->user()->authorizeRoles(['admin']);
        
        $usertype = UsersTypes::findOrFail($userId);
        $usertype->title = $request->name;
        $usertype->slug = str_slug($request->name, '-');
        $usertype->status = $request->status;
        
        if ( !$usertype->save() ){
            \Session::flash('error_msg','Fail at update.');
        }else{
            \Session::flash('success_msg','User type updated.');
        }
                
        return redirect('userstype/'.$userId.'/edit');
    }
    
    public function delete(Request $request, $userId){
        
        $request->user()->authorizeRoles(['admin']);
        
        $usertype = UsersTypes::findOrFail($userId);
        $usertype->delete();
        
        return redirect('userstype');
    }
}
