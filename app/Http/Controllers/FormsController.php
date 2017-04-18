<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormTemplate;

class FormsController extends Controller
{
    private $forms;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->forms = FormTemplate::all();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user()->authorizeRoles(['admin', 'staff']);
        
        return view('forms.list')->with('forms', $this->forms);
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
        return view('forms.form');
    }
}
