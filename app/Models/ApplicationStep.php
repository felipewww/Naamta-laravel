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
      'responsible', 'status', 'email_id', 'step_id', 'previous_step', 'application_id', 'morphs_from', 'title' ,'description' ,'status', 'ordination'
    ];

    public function form()
    {
        return $this->hasOne('App\Models\Form');
    }

    public function screen()
    {
        return $this->hasOne('App\Models\Screen');
    }

    public function step()
    {
        return $this->hasOne('App\Models\Step');
    }

//    public function application()
//    {
//        return $this->hasOne('App\Models\Application');
//    }

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
}
