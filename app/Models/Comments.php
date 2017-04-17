<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text', 'status', 'field', 'response', 'status'
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User');
    }
}
