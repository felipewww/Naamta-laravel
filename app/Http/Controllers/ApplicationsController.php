<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;

class ApplicationsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $applications;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = \Auth::user()->authorizeRoles(['admin']);;
            return $next($request);
        });
        $this->applications = Application::all();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('applications.list')->with('applications', $this->applications);
    }
}
