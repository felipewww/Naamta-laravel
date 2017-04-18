<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersTypes extends Model
{
    protected $table = 'user_types';
    protected $fillable = ['slug', 'title', 'status'];
}
