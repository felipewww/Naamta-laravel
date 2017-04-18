<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailTemplate;

class EmailsController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request){
        
        $request->user()->authorizeRoles(['admin']);
        
        $emails = EmailTemplate::all();
        
        return view('emails.show')->with('emails', $emails);
    }
    
    public function insert(Request $request){
        
        $request->user()->authorizeRoles(['admin']);
        
        return view('emails.edit');
    }
    
    public function insert_post(Request $request){
        
        $request->user()->authorizeRoles(['admin']);
        
        $this->validate($request, [
            'name' => 'required',
            'text' => 'required'
        ]);
        
        $data = array( 'title' => $request->name, 'text'=>$request->text, 'status'=>$request->status);
        $email = EmailTemplate::create($data);
        
        if ( !$email ){
            \Session::flash('error_msg','Email create failed.');
        }else{
            \Session::flash('success_msg','Email template created.');
        }
        
        return redirect('emails/create');
    }
    
    public function update(Request $request, $userId){
        
        $request->user()->authorizeRoles(['admin']);
        
        $email = EmailTemplate::findOrFail($userId);
        
        return view('emails.edit')->with('email', $email);
    }
    
    public function update_post(Request $request, $userId){
        
        $request->user()->authorizeRoles(['admin']);
        
        $this->validate($request, [
            'name' => 'required',
            'text' => 'required'
        ]);
        
        $email = EmailTemplate::findOrFail($userId);
        $email->title = $request->name;
        $email->text = $request->text;
        $email->status = $request->status;
        
        if ( !$email->save() ){
            \Session::flash('error_msg','Email create failed.');
        }else{
            \Session::flash('success_msg','Email template created.');
        }
        
        return redirect('emails/'.$userId.'/edit');
    }
    
    public function delete(Request $request, $userId){
        
        $request->user()->authorizeRoles(['admin']);
        
        $email = EmailTemplate::findOrFail($userId);
        $email->delete();
        
        return redirect('emails');
    }
}
