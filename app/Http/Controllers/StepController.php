<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Step;

class StepController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $steps;

    public function __construct()
    {
        $this->middleware('auth');
        $this->steps = Step::all();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user()->authorizeRoles(['admin']);

        return view('panel.steps.list')->with('steps', $this->steps);
    }
}
