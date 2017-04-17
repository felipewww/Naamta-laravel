<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormValue extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client', 'field', 'value'
    ];

    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }
}
