<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerEmails extends Model
{
    public $fillable = ['application_id', 'email'];
}
