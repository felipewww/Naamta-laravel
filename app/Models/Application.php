<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client', 'description', 'value'
    ];

    public function steps()
    {
        return $this->hasMany('App\Models\Step');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }

    public function client()
    {
        return $this->hasOne('App\Models\Client');
    }
}
