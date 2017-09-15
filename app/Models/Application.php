<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Application extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id', 'description', 'status', 'type'
    ];

    public function authorize()
    {
        $user = Auth::user();

        if ( !$user->hasRole(['admin','staff']) )
        {
            /*
             * If applicant didn't confirm registration yet, relations between user->application still not exists.
             * */
            if ($this->status != "wt_firstform" && $this->status != "denied")
            {
                $client = UserApplication::where('application_id', $this->id)->where('user_id', $user->id)->first();
                if (!$client) {
                    throw new \Error('Access denied');
                }
            }
        }

        return $this;
    }

    public function steps()
    {
        return $this->hasMany(ApplicationStep::class);
    }
    public function responsible()
    {
        return $this->hasOne('App\Models\User', 'id', 'staff_id');
    }

    public function users()
    {
        return $this->belongsTo(UserApplication::class, 'id', 'application_id');
    }

    public function client()
    {
        return $this->hasOne('App\Models\Client', 'id', 'client_id');
    }

    public function userTypes()
    {
        return $this->hasMany(ApplicationUserTypes::class);
    }

    public function customerEmails(){
        return $this->hasMany(CustomerEmails::class);
    }

    public function Verifiers()
    {
        return $this->hasMany(ApplicationVerifiers::class);
    }
}
