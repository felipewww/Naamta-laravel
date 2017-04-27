<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    /*
     * Staff types, into App\Role [Staff type] has many user types (staff 1, staff 2, staff 3...)
     * */
    protected $table = 'user_types';
    protected $fillable = ['slug', 'title', 'status'];
}
