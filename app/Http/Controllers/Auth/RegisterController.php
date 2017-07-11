<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApplicationsController;
use App\Models\User;
use App\Models\Role;
use App\Models\Client;
use App\Models\Application;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        parent::__construct();
//        $this->middleware('guest');
    }

    /*
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */

    public function register(Request $request)
    {
        dd('...', $request->all());
//        event(new Registered($user = $this->create($request->all())));

//        $this->guard()->login($user);

//        return $this->registered($request, $user) ? : redirect($this->redirectPath());
        return $this->registered($request, $user) ? : dd('...');
    }

//    protected function validator(array $data)
//    {
//        $validator = Validator::make($data, [
//            'company'  => 'required|max:255',
//            'name'     => 'required|max:255',
//            'email'    => 'required|email|max:255',
//            'password' => 'required|min:6|confirmed',
//        ]);
//
//        return $validator;
//    }

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
            $user = new User();
            $user->fillable([
                'name', 'password', 'status', 'see_apps', 'email'
            ]);

            $user->name      = $data['name'];
            $user->email     = $data['email'];
            $user->verified  = false;
            $user->password  = bcrypt($data['password']);
            $user->save();

            $user
                ->roles()
                ->attach(Role::where('name', 'client')->first());

            $client = Client::create([
                'company' => $data["company"],
                'user_id' => $user->id
            ]);

            //Generate token and send email
            $actService = new ActivationService();


            $application = new Application([
                'client_id' => $client->id,
                'description' => "Waiting for pre-approval",
                'status' => 'wt_emailconfirm'
            ]);

            /*
             * Set as TRUE to test the complete proccess (sending e-mails, waiting approval and etc. within 'local' or 'staging'
             * */
            $fullProccess = env('REGISTER_FULL_PROCESS', true);

            switch (app('env') )
            {
                case 'local' || 'staging' || 'dev':
                    if (!$fullProccess) {
                        $user->verified = true;
                        $user->save();
                        $application->status = '0';
                        $application->save();
                        $user->verified = true;
                        ApplicationsController::cloneApplication($application, $user);
                    }else{
                        $this->doFullProccess($application, $actService, $user);
                    }
                    break;

                case 'production':
                    $this->doFullProccess($application, $actService, $user);
                    break;
            }

            $application->save();

            \DB::commit();
            return $user;

        } catch (Exception $e){
            \DB::rollBack();
            throw $e;
        }
    }

    function doFullProccess(Application &$application, ActivationService &$actService, User &$user)
    {
        $actService->sendActivationMail($user);
    }

    public function emailConfirmation(Request $request, $token = null)
    {
        $actService = new \App\Http\Controllers\Auth\ActivationService();
        $actService->activateNewUser($token);
        return redirect()->to('/');
    }

    public function resendConfirmationToken(Request $request, $token = null, $id)
    {
        /*
         * Resend only if data combine
         * */
        $act = \App\Models\UserActivation::where(
            [
                'token' => $token,
                'user_id' => $id
            ])
            ->first();

        if (!$act) { throw new Error('Forbidden'); }

        $actService = new \App\Http\Controllers\Auth\ActivationService();
        $actService->sendActivationMail(\App\Models\User::findOrFail($id));

        Auth::logout();

        return redirect('/login')->with('resend', true);
    }
}
