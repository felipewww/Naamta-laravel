<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApplicationsController;
use App\Mail\AuthEmails;
use App\Models\Application;
use App\Models\User;
use App\Models\UserActivation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ActivationService
{
    protected $resendAfter = 1;

    public function sendActivationMail(User $user)
    {

        if ($user->verified || !$this->shouldSend($user)) {
            return;
        }

        $token = $this->createActivation($user);
        $this->sendMail($token, $user);
    }

    public function sendMail($token, User $user)
    {
        env('MAIL_FROM_NAME', 'Naamta Register');

        /*
         * If you're in development, set you e-mail in .ENV file to receive the confirmation email
         */
        if (app('env') == 'local') {
            $user->email = env('MAIL_LOCAL_RECEIVER');
        }

        Mail::to($user)->send(
            new AuthEmails('register', [
                'token' => $token,
                'user' => $user
            ])
        );
    }

    public function activateNewUser($token)
    {
        $activation = $this->getActivationByToken($token);

        if ($activation === null) { return null; }

        $user = User::where('id', $activation->user_id)->with(['client','client.application'])->first();
        $user->verified = true;

        $application = $user->client->application;

        $application->status = 'wt_firstform';
        $application->save();

        $user->save();

        $this->deleteActivation($token);
        //ApplicationsController::cloneApplication($user->client->application, $user);

        return $user;
    }

    /*
     * Verifica se ja venceu o prazo de reenvio do email, deixar sem funcionar por enquanto;
     * */
    private function shouldSend(User $user)
    {
        return true;
        $activation = $this->getActivation($user);
        return $activation === null || strtotime($activation->created_at) + 60 * 60 * $this->resendAfter < time();
    }

    //
    //REPOSITORY
    //

    protected function getToken()
    {
        return hash_hmac('sha256', str_random(40), config('app.key'));
    }

    public function createActivation(User $user)
    {
        $activation = $this->getActivation($user);

        if (!$activation) {
            return $this->createToken($user);
        }
        return $this->regenerateToken($user);
    }

    private function regenerateToken(User $user)
    {
        $token = $this->getToken();

        UserActivation::where('user_id', $user->id)->update([
            'token'         => $token,
            'created_at'    => new Carbon()
        ]);

        return $token;
    }

    private function createToken(User $user)
    {
        $token = $this->getToken();

        UserActivation::insert([
            'user_id' => $user->id,
            'token' => $token,
            'created_at' => new Carbon()
        ]);

        return $token;
    }

    public function getActivation($user)
    {
        return UserActivation::where('user_id', $user->id)->first();
    }

    /**
     * Should be hasOne method?
     */
    public function getActivationByToken($token)
    {
        return UserActivation::where('token', $token)->first();
    }

    public function deleteActivation($token)
    {
        UserActivation::where('token', $token)->first()->delete();
    }

}