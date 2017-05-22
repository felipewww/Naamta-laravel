<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivation extends Model
{
    protected $primaryKey   = 'user_id';
    public $timestamps      = false;
}
