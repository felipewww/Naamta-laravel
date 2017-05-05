<?php

namespace App\Http\Controllers;

use App\Library\DataTablesExtensions;
use App\Models\Application;
use App\Models\ApplicationStep;
use App\Models\ApplicationUsesEmail;
use App\Models\EmailTemplate;
use App\Models\FormTemplate;
use App\Models\Screens;
use App\Models\UserType;
use App\Models\UsesEmail;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Step;

class StepsController extends Controller
{
    //use DataTablesExtensions;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $steps;
    private $step;

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
        $vars = new \stdClass();
        $vars->steps        = Step::where('status', 1)->orderBy('ordination')->get();
        $vars->inactives    = Step::where('status', 0)->get();
        
        return view('steps.list', ['vars' => $vars]);
    }

    public function defaultVars($action = 'create', $step = null)
    {
        $vars = new \stdClass();
        $vars->steps            = $this->steps;
        $vars->morphs_from      = [FormTemplate::class, Screens::class];
        $vars->emailTemplates   = EmailTemplate::all();

        if ( $action == 'edit' )
        {
            if ($step instanceof ApplicationStep) {
                $vars->application = $step->application;
                $vars->userTypes = $step->application->userTypes;
                $vars->stepFrom = 'clone';
            }else{
                $vars->application = new Application();
                $vars->userTypes = UserType::all();
                $vars->stepFrom = 'default';
            }

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
                        array_push($usedEmails['success'][$template->id], $foundItem->received_by);
                    }
                }

                $search = $vars->emails_rejected->where('email_id', $template->id);
                $hasRejected = $search->isNotEmpty();

                if ($hasRejected) {
                    $usedEmails['rejected'][$template->id] = [];
                    foreach ($search as $foundItem)
                    {
                        array_push($rejected, $foundItem->id);
                        array_push($usedEmails['rejected'][$template->id], $foundItem->received_by);
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
            $vars->application  = new Application();
            $vars->stepFrom = 'default';
            $vars->usedEmails   = false;
            $vars->userTypes    = UserType::all();
        }

        //dd($vars);

        return $vars;
    }

    public function create()
    {
        $vars = $this->defaultVars();

        return view('steps.form', ['vars' => $vars]);
    }

    public function update($id, Request $request)
    {
        \DB::beginTransaction();

        switch ($request->_stepFrom)
        {
            case 'default':
                $this->step = Step::findOrFail($id);
                $redirect = '/steps/'.$id.'/edit';
                break;

            case 'clone':
                $this->step = ApplicationStep::findOrFail($id);
                $redirect = '/applications/step/'.$id;
                break;
        }

        /*
         * The table "app_uses_emails" is used also a N>M relation between app_steps and app_user_types, and it will also used to identify
         * which user type will receive e-mails each step.
         *
         * So, to update this N>M relation (app_uses_emails table), we need to use the userType method, just because the table name don't use
         * common relation name.
         *
         * Remember! Always we get the $this->step, it can be AppStep or Step (system default) and the models needs have same methods
         * such as userType, usesEmails with same names, but, searching in different tables.
         * */
        $this->step->userTypes()->detach();

        if ($request->usedemails)
        {
            foreach ($request->usedemails as $action => $email)
            {
                foreach ($email as $data)
                {
                    $email_id   = $data['id'];
                    $new_staffs = $data['staffs'];

                    $this->step->userTypes()->attach(
                        $new_staffs,
                        [
                            'email_id' => $email_id,
                            'send_when' => $action,
                        ]
                    );
                }
            }
        }

        $this->verifyNewEmails('clone');
        $request->offsetUnset('usedemails');

        $this->step->update($request->all());

        \DB::commit();

        return redirect($redirect);
    }

    public function store(Request $request)
    {
        $success = $request->emails_success;
        $rejected = $request->emails_rejected;

        $request->offsetUnset('emails_success');
        $request->offsetUnset('emails_rejected');

        $lastStep = Step::find(1)->orderBy('ordination')->get()->last();

        if ( !$lastStep ) {
            $lastStep = new Step();
            $lastStep->id = null;
        }

        $request->offsetSet('ordination', Step::count());
        $request->offsetSet('previous_step', $lastStep->id);

        $this->step = Step::create($request->all());
        $this->verifyNewEmails('default');

        return $this->create();
    }

    private function verifyNewEmails($from)
    {
        switch ($from)
        {
            case 'default':
                $key = 'step_id';
                $model = UsesEmail::class;
                break;

            case 'clone':
                $key = 'application_step_id';
                $model = ApplicationUsesEmail::class;
                break;

            default:
                throw new \Error('$from is not defined. Contact the system admin.');
        }

        $request = Request::capture();

        if ( !empty($request->emails_success) || !empty($request->emails_rejected) )
        {
            $emails = [
                'success' => $request->emails_success,
                'rejected' => $request->emails_rejected
            ];

            foreach ($emails as $send_when => $sync)
            {
                if (!is_array($sync)) { $sync = []; }
                foreach ($sync as $data)
                {
                    $templateID = $data[0][0];
                    foreach ($data[1] as $userTypeID)
                    {
                        $reg = [
                            $key       => $this->step->id,
                            'email_id'      => $templateID,
                            'received_by'   => $userTypeID,
                            'send_when'     => $send_when,
                        ];

                        try{
                            $model::create($reg);
                        }catch (QueryException $e){
                            //TODO - Create a message to notify user that this e-mail wasn't saved on DB, because FK already exists.
                            continue;
                        }
                    }
                }
            }
        }
    }

    public function appStep($id)
    {
        $step = ApplicationStep::findOrFail($id);
        return $this->edit($step);
    }

    public function edit($id)
    {
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

    public function saveDefaultStepsPosition(Request $request)
    {
//        dd($request->all());
        \DB::beginTransaction();
        $previous_step = null;
        $i = 0;
        while ($i < count($request->ids))
        {
            $stepID = $request->ids[$i];

            $step = Step::findOrFail($stepID);
            $step->previous_step = $previous_step;
            $step->ordination = $i;
            $step->status = 1;
            $step->save();
            $previous_step = $stepID;
            $i++;
        }

        $ix = 0;
        while ( $ix < count($request->toInactive) )
        {
            $stepID = $request->toInactive[$ix];

            $step = Step::findOrFail($stepID);
            $step->previous_step = null;
            $step->ordination = 0;
            $step->status = 0;
            $step->save();
            $ix++;
        }
        \DB::commit();

        return json_encode(['status' => true]);
    }

//    public function dataTablesConfig()
//    {
//        $data = [];
//        foreach ($this->steps as $reg)
//        {
//            $newInfo = [
//                $reg['id'],
//                $reg['title'],
//                ($reg['status']) ? 'Active' : 'Inactive',
//                [
//                    'rowActions' =>
//                        [
//                            [
//                                'html' => '',
//                                'attributes' => ['class' => 'fa fa-pencil', 'href' => '/steps/'.$reg->id.'/edit']
//                            ],
//                            [
//                                'html' => '',
//                                'attributes' => ['class' => 'fa fa-trash']
//                            ],
//                            [
//                                '' => 'formdelete'
//                            ]
//                        ]
//                ]
//            ];
//
//            array_push($data, $newInfo);
//        }
//
//        $this->data_info = $data;
//        $this->data_cols = [
//            ['title' => 'id', 'width' => '40px'],
//            ['title' => 'Title'],
//            ['title' => 'Status'],
//            ['title' => 'Actions'],
//        ];
//    }
}
