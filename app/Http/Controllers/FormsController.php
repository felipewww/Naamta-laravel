<?php

namespace App\Http\Controllers;

use App\Library\DataTablesExtensions;
use Illuminate\Http\Request;
use App\Models\FormTemplate;

class FormsController extends Controller
{
    use DataTablesExtensions;
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
        $this->dataTablesInit();
        return view('forms.list', ['dataTables' => $this->dataTables]);
    }

    public function dataTablesConfig()
    {
        $data = [];
        foreach ($this->forms as $reg)
        {
            $newInfo = [
                $reg['id'],
                $reg['name'],
                ($reg['status']) ? 'Active' : 'Inactive',
                [
                    'rowActions' =>
                        [
                            [
                                'html' => 'edit',
                            ],
                            [
                                'html' => 'delete',
                            ]
                        ]
                ]
            ];

            array_push($data, $newInfo);
        }

        $this->data_info = $data;
        $this->data_cols = [
            ['title' => 'id', 'width' => '40px'],
            ['title' => 'Name'],
            ['title' => 'Status'],
            ['title' => 'Actions'],
        ];
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
