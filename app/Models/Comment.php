<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'userId', 'userName', 'text', 'field'
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User');
    }
}
