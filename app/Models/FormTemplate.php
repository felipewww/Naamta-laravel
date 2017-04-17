<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormTemplate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'container', 'status'
    ];

    public function containers()
    {
        return $this->hasMany('App\Models\Container');
    }
}
