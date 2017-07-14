<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = 'roles';

    public function Users(){
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }
}
