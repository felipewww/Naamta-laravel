<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
 
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;

class EmailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $emails;

    public function __construct()
    {
        $this->middleware('auth');
        $this->emails = EmailTemplate::all();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user()->authorizeRoles(['admin']);

        return view('panel.emails.list')->with('emails', $this->emails);
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
        return view('panel.emails.form');
    }

     /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $rules = array(
            'title' => 'required',
            'text'  => 'required',
        );
        
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('email/create')
                ->withErrors($validator)
                ->withInput();
        }
          
        // store
        $email = new EmailTemplate();
        $email->title = Input::get('title');
        $email->text = Input::get('text');
        $email->save();

        // redirect
        Session::flash('message', 'Item cadastrado com sucesso!');
        
        return Redirect::to('email');
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit($id)
    {
        // get all the nerds
         $email = EmailTemplate::find($id);

        // show the view and pass the nerd to it
        return view('panel.emails.form')
            ->with(array('email' => $email));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function update($id)
    {
        $rules = array(
            'title' => 'required',
            'text'  => 'required',
        );
        
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('email/create')
                ->withErrors($validator)
                ->withInput();
        }
          
        // store
        $email = EmailTemplate::Find($id);
        $email->title = Input::get('title');
        $email->text = Input::get('text');
        $email->update();

        // redirect
        Session::flash('message', 'Item atualizado com sucesso!');
        
        return Redirect::to('email');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        // delete
        try{
            $email = EmailTemplate::find($id);
            $email->delete();
            Session::flash('message', 'Item deletado!');
            
        }catch (Exception $e){
            Session::flash('message', 'Erro ao apagar os itens!');
        }

        return Redirect::to('email');
        
    }
}
