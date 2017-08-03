<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ApplicationStep extends Model
{
    public $table = 'application_steps';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'responsible',
        'status',
        'email_id',
        'step_id',
        'previous_step',
        'application_id',
        'morphs_from',
        'morphs_id',
        'title',
        'description',
        'status',
        'ordination'
    ];

    public function screen()
    {
        return $this->hasOne(Screen::class);
    }

    public function step()
    {
        return $this->hasOne(Step::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }

    public function usesEmails()
    {
        return $this->hasMany(ApplicationUsesEmail::class);
    }

    public function userTypes()
    {
        return $this->belongsToMany(ApplicationUserTypes::class, 'application_uses_emails', 'application_step_id', 'received_by');
    }

    public function Forms()
    {
        return $this->hasMany(ApplicationStepForms::class);
    }

    public function SQLForms()
    {
        return $this->hasMany(ApplicationStepForms::class);
    }

    public function Approval()
    {
        return $this->hasOne(Approval::class, 'id', 'morphs_id');
    }

    public function previousStep(){
        $prev = ApplicationStep::where("ordination", (($this->ordination > 0 ? ($this->ordination - 1): 0)))
            ->where('application_id', $this->application->id)
            ->first();

        if($prev->id === $this->id){
            return null;
        }
        return $prev;
    }

    public function nextStep(){
        $next = ApplicationStep::where('application_id', $this->application_id)->where("ordination", ($this->ordination + 1))->first();

        return $next;
    }

    public function loggedUserIsResponsible()
    {
        $user = Auth::user();
        $try = UserApplication::where('user_type', $this->responsible)->where('user_id', $user->id)->first();

        return ( $try == null ) ? false : true;
    }

    public function loggedUserIsStepResponsible()
    {
        $currentUserType = $this->application->users()->where('user_id', Auth::user()->id)->get();

        if ($currentUserType->count() == 1)
        {
            $currentUserType = $currentUserType->first();
            $isResponsible = ($this->responsible == $currentUserType->user_type);
        }
        else
        {
            $isResponsible = $currentUserType->where('user_type', $this->responsible)->first();
        }

        return $isResponsible;
    }
}
