<?php

namespace App\Http\Controllers\Auth;

use App\Models\ApplicationStep;
use App\Models\ApplicationUserTypes;
use App\Models\ApplicationUsesEmail;
use App\Models\Step;
use App\Models\User;
use App\Models\Role;
use App\Models\Client;
use App\Models\Application;

use App\Http\Controllers\Controller;
use App\Models\UserApplication;
use App\Models\UserType;
use App\Models\UsesEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /*
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'company'  => 'required|max:255',
            'name'     => 'required|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /*
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        \DB::beginTransaction();

        try{
            
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password'])
            ]);
        
            $user   
                ->roles()
                ->attach(Role::where('name', 'client')->first());

            $uTypes = UserType::all();

            $client = Client::create([
                'company' => $data["company"],
                'user_id' => $user->id
            ]);

            $application = Application::create([
                'client_id' => $client->id,
                'description' => " ",
                'status' => 0
            ]);

            /*
             * Clone user types default with new ids.
             * */
            $uTypesClones = []; //temp relations between new id (will be generated in this loop) and old id.
            foreach ($uTypes as $cloneType)
            {
                $defaultID = $cloneType->id;
                unset($cloneType['id']);
                unset($cloneType['created_at']);
                unset($cloneType['updated_at']);

                $cloneType->setAttribute('application_id', $application->id);

                $newAppType = ApplicationUserTypes::create($cloneType->getAttributes());
                $uTypesClones[$defaultID] = $newAppType->id;
            }

            $clientType = ApplicationUserTypes::create([
                'slug' => 'client',
                'title' => 'Client',
                'status' => 1,
                'application_id' => $application->id,
            ]);
            /*
             * Create application user where his type is the last type found
             * */
            $appUsers = UserApplication::create([
                'application_id' => $application->id,
                'user_id' => $user->id,
//                'user_type' => $newAppType->id,
                'user_type' => $clientType->id,
            ]);
//            dd($uTypesClones);

            /*
             * Clone default steps with new ID
             * */
            $defaultSteps = Step::where('status', 1)->get();
            $default_ids = [];
            foreach ($defaultSteps as $step)
            {
                $newRefID = null;
                if ($step->previous_step) {
                    $newRefID = $default_ids[$step->previous_step];
                }

                $appSteps = ApplicationStep::create([
                    'application_id'    => $application->id,
                    'previous_step'     => $newRefID,
                    'responsible'       => $uTypesClones[$step->responsible], //Keep the usertype relation with new id
                    'title'             => $step->title,
                    'description'       => $step->description,
                    'ordination'        => $step->ordination,
                    'status'            => '0',
                    'morphs_from'       => $step->morphs_from,
                ]);

                $default_ids[$step->id] = $appSteps->id;

                /*
                 * Clone UsesEmails default with new IDs using default temporary relationship user types
                 * */
                $emails = UsesEmail::where('step_id', $step->id)->get();

                foreach ($emails as $clone)
                {

                    $cloneID = $clone->received_by;
                    unset($clone['id']);
                    unset($clone['step_id']);
                    unset($clone['received_by']);
                    $clone->application_step_id = $appSteps->id;
                    $newEmailRelation = new ApplicationUsesEmail($clone->getAttributes());

                    $newEmailRelation->received_by = $uTypesClones[$cloneID]; //$newAppType->id;
                    $newEmailRelation->save();
                }
            }


            \DB::commit();
//            dd('here');
            return $user;

        } catch (Exception $e){
            \DB::rollBack();
            throw $e;
        }        

    }
}
