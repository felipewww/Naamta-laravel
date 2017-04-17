<?php

namespace App\Models;

class Client extends User
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company'
    ];

}
