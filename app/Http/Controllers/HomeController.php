<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\ActivationService;
use App\Models\Application;
use App\Models\Approval;
use App\Models\ClientFirstForm;
use App\Models\FormTemplate;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\ApplicationStep;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $vars;

    public function __construct()
    {
        parent::__construct();
        $this->vars = new \stdClass();
        $this->middleware(function ($request, $next) {
            $user = \Auth::user()->authorizeRoles(['admin', 'staff', 'client']);;
            return $next($request);
        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Auth::user()->verified) {
            $activation = new ActivationService();
            return view('homes.wait_emailverify', 
                [
                    'pageInfo' => $this->pageInfo,
                    'user' => Auth::user(),
                    'token' => $activation->getActivation(Auth::user())->token
                ]
            );
        }

        $userType = Auth::user()->roles[0]->name;

        $this->pageInfo->title              = Auth::user()->name."'s".' Dashboard';
        $this->pageInfo->category->title    = $userType;
        $this->pageInfo->subCategory->title = 'Homepage';
        $this->vars->userType = $userType;

        if($userType === "client"){
            $application = Client::where("user_id", Auth::id())->first()->application()->first();
            if(isset($application)){
                if($application->status == '0' || $application->status == 'wt_payment'){
                    return view('homes.wait_approval', ['pageInfo' => $this->pageInfo]);
                }
                return $this->applicationDashboard($request, $application->id);
            }
        }
        
        $this->vars->activeApplications = Application::where('status', '1')->get();
        $this->vars->inactiveApplications = Application::whereIn('status', ['0', 'wt_payment'])->get();

        return view('homes.admin', ['vars' => $this->vars, 'pageInfo' => $this->pageInfo]);
    }

    public function applicationDashboard(Request $request, $id)
    {
        $application = Application::with(['steps'])->find($id);
        $user = Auth::user();

        if ( $application->status == 'wt_firstform' ) {
            $this->pageInfo->title              = $user->name."'s".' Registration';
            $this->pageInfo->title              = Auth::user()->name."'s".' Registration';
            $this->pageInfo->category->title    = 'Registration';
            $this->pageInfo->subCategory->title = 'Form';
            $this->vars->userType = '$userType';
            $client = $user->client;
            $form = $client->firstForm;

            $faker = Factory::create();
            $required = 'required="required"';
            if ( app('env') == 'local' && !$form )
            {
                $required = '';

                $form = new ClientFirstForm([
                    'client_id' => $client->id,
                    'status' => '0',
                    'services_accredited' => 'medical_transport',
                    'taxpayer_id' => '/uploads/taxpayer_id.pdf',
                    'address_street' => $faker->address,
                    'address_mailing' => $faker->address,
                    'phone_number' => $faker->phoneNumber,
                    'business_type' => 'corporation',
                    'website' => $faker->url,
                    'ownerships' => 'government agency, hospital, "dba", charter services, medical transport services, corporations, subsidiaries.',
                    'contact_name' => $faker->name,
                    'contact_email' => $faker->email,
                    'contact_phone' => $faker->phoneNumber,
                    'compliance_name' => $faker->company,
                    'compliance_email' => $faker->companyEmail,
                    'compliance_phone' => $faker->phoneNumber,
                    'application_access' => $faker->companyEmail.','.$faker->companyEmail.','.$faker->companyEmail ,
                    'since' => date('Y-m-d G:i:s',$faker->dateTime->getTimestamp()),
                    'transports_per_year' => $faker->randomNumber(3),
                    'base_locations' => $faker->address,
                    'communications_center' => $faker->address,
                    'description' => $faker->paragraph(5),
                    'patient_population' => 'adult',
                    'medical_director_name' => $faker->name,
                    'medical_based' => $faker->paragraph,
                    'medical_drug_license' => '/uploads/drug_license_file.pdf',
                    'customer_reference_letter_1' => '/uploads/file_customer_reference_letter.pdf',
                    'customer_reference_letter_2' => '/uploads/file_customer_reference_letter.pdf',
                    'signed_acknowledgment_doc' => '/uploads/file_signed_acknowledgment_doc.pdf',
                ]);
            }

            return view('applications.first_form',[
                'application'   => $application,
                'pageInfo'      => $this->pageInfo,
                'withAction'    => true,
                'form'          => $form,
                'required'      => $required
            ]);

        }
        else
        {
            /*
             * Verify if current user has some responsibility about this step
             * */
            $currentStep = $application->steps->where("status", "current")->first();

            if($currentStep===null){
                $currentStep = $application->steps->first();
            }

            $currentUserType = $application->users()->where('user_id', $user->id)->get();

            if ($currentUserType->count() == 1)
            {
                $currentUserType = $currentUserType->first();
                $isResponsible = ($currentStep->responsible == $currentUserType->user_type);
            }
            else
            {
                $isResponsible = $currentUserType->where('user_type', $currentStep->responsible)->first();
            }
            /*
             * End verify responsible
             * */

            $stepsWithForm = $application->steps->where("morphs_from", FormTemplate::class )->all();
            $approvalWithReport = $application->steps->where('morphs_from', Approval::class)->where('approval.has_report', '1')->all();
            $reports = array();
            foreach($approvalWithReport as $approval){
                $step = ApplicationStep::findOrFail($approval->id);
                if($step->Approval->report!=null){
                    array_push($reports, array('stepId' => $approval->id, 'report' => $step->Approval->report));
                }
            }

            return view('homes.application', [
                'pageInfo'              => $this->pageInfo,
                'application'           => $application,
                'stepsWithForm'         => $stepsWithForm,
                'approvalWithReport'    => $reports,
                'currentStep'           => $currentStep,
                'isResponsible'         => $isResponsible
            ]);
        }
    }
}
