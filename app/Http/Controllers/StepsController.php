<?php

namespace App\Http\Controllers;

use App\Library\DataTablesExtensions;
use App\Models\EmailTemplate;
use App\Models\FormTemplate;
use App\Models\Screens;
use App\Models\UserType;
use App\Models\UsesEmail;
use Illuminate\Http\Request;
use App\Models\Step;

class StepsController extends Controller
{
    use DataTablesExtensions;
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
        $this->dataTablesInit();
        return view('steps.list', ['dataTables' => $this->dataTables]);
    }

    public function create()
    {   
        $vars                   = new \stdClass();
        $vars->steps            = $this->steps;
        $vars->morphs_from      = [FormTemplate::class, Screens::class];
        $vars->forms            = FormTemplate::all();
        $vars->screens          = Screens::all();
        $vars->emailTemplates   = EmailTemplate::all();
        $vars->userTypes        = UserType::all();

        return view('steps.form', ['vars' => $vars]);
    }

    public function store(Request $request)
    {
        switch ($request->morphs_from)
        {
            case FormTemplate::class:
                $fk_id = $request->forms;
                $fk_str = 'form';
                break;

            case Screens::class:
                $fk_id = $request->screens;
                $fk_str = 'screen';
                break;

            default:
                throw trigger_error('Unexpected error. Please, contact the administrator.', E_USER_ERROR);
                break;
        }

        $request->offsetSet($fk_str, $fk_id);

        $emails = [
            'success' => $request->emails_success,
            'rejected' => $request->emails_rejected,
        ];

        $request->offsetUnset('forms');
        $request->offsetUnset('screens');
        $request->offsetUnset('emails_success');
        $request->offsetUnset('emails_rejected');

        $step = Step::create($request->all());

        foreach ($emails as $send_when => $sync)
        {
            if (!is_array($sync)) { $sync = []; }
            foreach ($sync as $data)
            {
                $templateID = $data[0][0];
                foreach ($data[1] as $userTypeID)
                {
                    $reg = [
                        'step_id'       => $step->id,
                        'email_id'      => $templateID,
                        'received_by'   => $userTypeID,
                        'send_when'     => $send_when,
                    ];

                    UsesEmail::create($reg);
                }
            }
        }
//        dd($reg);

        return $this->create();
    }

    public function dataTablesConfig()
    {
        $data = [];
        foreach ($this->steps as $reg)
        {
            $newInfo = [
                $reg['id'],
                $reg['title'],
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
            ['title' => 'Title'],
            ['title' => 'Status'],
            ['title' => 'Actions'],
        ];
    }
}
