<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationStep extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'responsible', 'status', 'step_id', 'previous_step', 'application_id', 'morphs_from', 'title' ,'description' ,'status'
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
}
