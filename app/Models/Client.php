<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id', 'company', 'user_id'
    ];

    public function user()
    {
        return $this->belongsToOne('App\Models\User', 'user_id');
    }
}
