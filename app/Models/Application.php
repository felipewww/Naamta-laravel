<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        //return $this->hasMany('App\Models\User');
    }

    public function client()
    {
        return $this->hasOne('App\Models\Client', 'id', 'client_id');
    }

    public function userTypes()
    {
        return $this->hasMany(ApplicationUserTypes::class);
    }
}
