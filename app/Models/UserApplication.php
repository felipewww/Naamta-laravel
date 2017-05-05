<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserApplication extends Model
{
    protected $table = 'application_users';
    protected $fillable = ['application_id', 'user_id', 'user_type'];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function type()
    {
        return $this->hasOne('App\Models\UserType', 'id', 'user_type');
    }

    public function appType()
    {
        return $this->belongsTo(ApplicationUserTypes::class, 'user_type', 'id');
    }
}
