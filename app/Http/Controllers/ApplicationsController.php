<?php

namespace App\Http\Controllers;
use App\Library\DataTablesExtensions;
use Validator;
use Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\User;

class ApplicationsController extends Controller
{
    use DataTablesExtensions;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $applications;
    private $staffs;
    private $rules = [
        'description' => 'required|min:3|max:255',
        'staff_id'    => 'required'
    ];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = \Auth::user()->authorizeRoles(['admin']);;
            return $next($request);
        });
        $this->applications = Application::all();
        foreach(User::all() as $u){
            if($u->hasRole("staff"))
                $this->staffs[] = $u;
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->dataTablesInit();
        return view('applications.list', ['dataTables' => $this->dataTables ]);
    }

    public function edit(Request $request, $id)
    {
        $application = Application::FindOrFail($id);
        return view('applications.form', ['application' => $application, 'staffs' => $this->staffs ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), $this->rules)->validate();
        
        try{
            Application::where('id', $id)->update([
                'description'  => $request->description,
                'staff_id'     => $request->staff_id
            ]);

            \Session::flash('success','Application updated: ' . $request->title);
        } catch(Exception $e){
            \Session::flash('error','Application update failed: ' . $e);
        }

        return Redirect::to('applications');

    }


    private function dataTablesConfig()
    {
        $data = [];
        foreach ($this->applications as $reg)
        {
            $newInfo = [
                $reg->id,
                $reg->client->company,
                ($reg->status) ? 'Active' : 'Inactive',
                [
                    'rowActions' =>
                    [
                        [
                            'html' => '',
                            'attributes' => ['class' => 'fa fa-pencil']
                        ],
                        [
                            'html' => '',
                            'attributes' => ['class' => 'fa fa-trash']
                        ]
                    ]
                ]
            ];
            array_push($data, $newInfo);
        }

        $this->data_info = $data;
        $this->data_cols = [
            ['title' => 'id', 'width' => '40px'],
            ['title' => 'Client'],
            ['title' => 'Status'],
            ['title' => 'Actions', 'width' => '100px'],
        ];
    }
}
