<?php

namespace App\Http\Controllers;
use App\Library\DataTablesExtensions;
use App\Models\ApplicationStep;
use App\Models\ApplicationUsesEmail;
use App\Models\Step;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Validator;
use Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\User;
use App\Models\UserType;
use App\Models\UserApplication;

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
    private $userTypes;
    private $usersApplication;

    private $rules = [
        'description' => 'required|min:3|max:255',
        'staff_id'    => 'required'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            $user = \Auth::user()->authorizeRoles(['admin']);;
            return $next($request);
        });
        $this->applications     = Application::all();
        $this->userTypes        = UserType::All();

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
        $this->pageInfo->title              = 'All Applications';
        $this->pageInfo->category->title    = 'Applications';
        $this->pageInfo->subCategory->title = 'List';

        $this->dataTablesInit();
        return view('applications.list', ['dataTables' => $this->dataTables, 'pageInfo' => $this->pageInfo]);
    }

    public function edit(Request $request, $id)
    {
        $this->pageInfo->title              = 'Application Steps';
        $this->pageInfo->category->title    = 'Application';
        $this->pageInfo->subCategory->title = 'Edit Steps';

        $this->usersApplication = UserApplication::where('application_id', $id)->get();
        $application = Application::FindOrFail($id);
        
        $steps = $application->steps()->with(['usesEmails', 'usesEmails.receivedBy', 'usesEmails.template'])->orderBy('ordination')->get();

        return view('applications.form',
            [
                'application'       => $application,
                'staffs'            => $this->staffs,
                'userTypes'         => $this->userTypes,
                'usersApplication'  => $this->usersApplication,
                'steps'             => $steps,
                'pageInfo'          => $this->pageInfo
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, $id, Response $res)
    {
        $validator = Validator::make($request->all(), $this->rules)->validate();
        \DB::beginTransaction();
        try{
            $application = Application::where('id', $id)->first();

            Application::where('id', $id)->update([
                'description'  => $request->description,
                'staff_id'     => $request->staff_id,
                'status'     => $request->status,
            ]);

            if(is_array($request->users_application)){
                UserApplication::where('application_id', $id)->delete();
                foreach($request->users_application as $uApp){
                    $_arrUApp = explode(",", $uApp);
                    $userApplication  = UserApplication::create([
                        'application_id'  => $id,
                        'user_id'         => trim(explode(",", $uApp)[0]),
                        'user_type'       => trim(explode(",", $uApp)[1]),
                    ]);
                }
            }
            \DB::commit();
            \Session::flash('success','Application updated: ' . $request->title);
        } catch(Exception $e){
            \Session::flash('error','Application update failed: ' . $e);
            \DB::rollBack();
            throw $e;
        }
        return Redirect::to('applications');

    }

    public function settings(Request $request, $id)
    {
        $this->pageInfo->title              = 'Application Settings';
        $this->pageInfo->category->title    = 'Application';
        $this->pageInfo->subCategory->title = 'Edit Settings';

        $usersApplication = UserApplication::with(['user','appType'])->where('application_id', $id)->get();
        $application    = Application::FindOrFail($id);
        $userTypes      = $application->userTypes;
        $staffs         = User::all();
        $hasInactiveSteps = $application->steps()->where('status', 0)->get()->count();

        return view('applications.edit',
            [
                'application' => $application,
                'userTypes' => $userTypes,
                'staffs' => $staffs,
                'usersApplication'  => $usersApplication,
                'hasInactiveSteps'  => $hasInactiveSteps,
                'pageInfo' => $this->pageInfo
            ]
        );
    }

    public function saveStepsPosition($appID, Request $request)
    {
        $application = Application::FindOrFail($appID);

        \DB::beginTransaction();
        $previous_step = null;
        $i = 0;
        while ($i < count($request->ids))
        {
            $stepID = $request->ids[$i];
            
            $step = ApplicationStep::findOrFail($stepID);
            $step->previous_step = $previous_step;
            $step->ordination = $i;
            $step->save();
            $previous_step = $stepID;
            $i++;
        }
        \DB::commit();

        return json_encode(['status' => true]);
    }

    public function changeStepStatus(Request $request)
    {
        $step           = ApplicationStep::where('id', $request->id)->first();
        $newStatus      = ($request->currentStatus == '1') ? 0 : 1;
        $step->status   = $newStatus;
        $step->save();

        $res = [
            'reqStatus' => true,
            'newStatus' => $newStatus
        ];
        
        return json_encode($res);
    }

    public function deleteStep(Request $request)
    {
        Step::where('id', $request->id)->delete();
        
        $res = [
            'status' => true
        ];

        return json_encode($res);
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
                            'attributes' => ['class' => 'btn btn-warning btn-circle fa fa-pencil m-l-10', 'href' => '/applications/'.$reg->id.'/edit']
                        ],
                        [
                            'html' => '',
                            'attributes' => ['class' => 'btn btn-danger btn-circle fa fa-trash m-l-10']
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
