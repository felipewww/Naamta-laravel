<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'responsible', 'previous_step', 'form', 'screen', 'status'
    ];

    public function usesEmails()
    {
        return $this->hasMany('App\Models\UsesEmail');
    }
}
