<?php

namespace App\Http\Controllers;

use App\Library\DataTablesExtensions;
use App\Models\ApplicationStep;
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

    public function defaultVars($action = 'create', $step = null)
    {
        $vars = new \stdClass();
        $vars->steps            = $this->steps;
        $vars->morphs_from      = [FormTemplate::class, Screens::class];
        $vars->emailTemplates   = EmailTemplate::all();

        if ( $action == 'edit' )
        {
            $vars->userTypes = ($step instanceof ApplicationStep) ? $step->application->userTypes : $vars->userTypes = UserType::all();
//            if ($step instanceof ApplicationStep) {
//                $vars->userTypes = $step->application->userTypes;
//            }else{
//                $vars->userTypes = UserType::all();
//            }

            foreach ($vars->steps as $step_temp)
            {
                $step_temp->selected = ( $step_temp->id == $step->previous_step ) ? 'selected' : '' ;
            }

            foreach ($vars->userTypes as $type_temp)
            {
                $type_temp->selected = ( $type_temp->id == $step->responsible ) ? 'selected' : '' ;
            }

            $emails_success         = $step->usesEmails()->with(['template'])->where('send_when', 'success')->get();
            $vars->emails_success   = $emails_success;

            $emails_rejected        = $step->usesEmails()->with(['template'])->where('send_when', 'rejected')->get();
            $vars->emails_rejected  = $emails_rejected;

            $usedEmails = [
                'success' => [],
                'rejected' => []
            ];

            foreach ($vars->emailTemplates as $template)
            {
                $success = [];
                $rejected = [];

                //Verify if the template is selected
                $search = $vars->emails_success->where('email_id', $template->id);
                $hasSuccess = $search->isNotEmpty();

                if ($hasSuccess) {
                    $usedEmails['success'][$template->id] = [];
                    foreach ($search as $foundItem)
                    {
                        array_push($success, $foundItem->id);
                        array_push($usedEmails['success'][$template->id], $foundItem->id);
                    }
                }

                $search = $vars->emails_rejected->where('id', $template->id);
                $hasRejected = $search->isNotEmpty();

                if ($hasRejected) {
                    $usedEmails['rejected'][$template->id] = [];
                    foreach ($search as $foundItem)
                    {
                        array_push($rejected, $foundItem->id);
                        array_push($usedEmails['rejected'][$template->id], $foundItem->id);
                    }
                }

                $template->setAttribute('emails', [
                        'success' => $success,
                        'rejected' => $rejected,
                    ]
                );
            }

            $vars->usedEmails = $usedEmails;
            $vars->functest = function($opt, $opts){
                $str = '';
                if ( $opt == 1 ) {
                    $str = 'selected';
                }
                return $str;
            };
        }
        else
        {
            $vars->step         = new Step();
            $vars->usedEmails   = false;
            $vars->userTypes    = UserType::all();
        }

        return $vars;
    }

    public function create()
    {
        $vars = $this->defaultVars();

        return view('steps.form', ['vars' => $vars]);
    }

    public function store(Request $request)
    {
        $emails = [
            'success' => $request->emails_success,
            'rejected' => $request->emails_rejected,
        ];

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

        return $this->create();
    }

    public function appStep($id)
    {
        $step = ApplicationStep::findOrFail($id);
        return $this->edit($step);
    }

    public function edit($id)
    {
        $step = ( $id instanceof ApplicationStep ) ? $id : Step::findOrFail($id);
        if ( $id instanceof ApplicationStep ) {
            $step = $id;
            $action = 'edit';
        }else{
            $step = Step::findOrFail($id);
            $action = 'edit';
        }

        $vars = $this->defaultVars($action, $step);
        $vars->step = $step;

        return view('steps.form', ['vars' => $vars]);
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
                                'html' => '',
                                'attributes' => ['class' => 'fa fa-pencil', 'href' => '/steps/'.$reg->id.'/edit']
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
            ['title' => 'Title'],
            ['title' => 'Status'],
            ['title' => 'Actions'],
        ];
    }
}
