<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tests extends Model
{
    public $fillable = ['value', 'name'];
    public $timestamps = false;
}
