<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'morphs_json',
        'title' ,'description' ,'status',
        'ordination'
    ];

    public function screen()
    {
        return $this->hasOne('App\Models\Screen');
    }

    public function step()
    {
        return $this->hasOne('App\Models\Step');
    }

    public function application()
    {
        return $this->belongsTo('App\Models\Application', 'application_id', 'id');
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
        return $this->hasOne(ApplicationStepApprovals::class);
    }

    public function previousStep(){
        $prev = ApplicationStep::where("ordination", (($this->ordination>0 ? ($this->ordination - 1): 0)))->first();
        if($prev->id === $this->id){
            return null;
        }
        return $prev;
    }

    public function nextStep(){
        $next = ApplicationStep::where("ordination", ($this->ordination + 1))->first();
        if($next->id === $this->title){
            return null;
        }

        return $next;
    }
}
