<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsesEmail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email_template_id', 'user_types_id'
    ];
}
