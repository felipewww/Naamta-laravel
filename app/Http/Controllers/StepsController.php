<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Step;

class StepsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $steps;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = \Auth::user()->authorizeRoles(['admin']);;
            return $next($request);
        });
        $this->steps = Step::all();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('steps.list')->with('steps', $this->steps);
    }
}
