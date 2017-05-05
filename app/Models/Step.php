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
        'title', 'description', 'responsible', 'previous_step', 'status', 'morphs_from', 'ordination'
    ];

    public function usesEmails()
    {
        return $this->hasMany(UsesEmail::class);
    }

    public function userTypes()
    {
        return $this->belongsToMany(UserType::class, 'uses_emails', 'step_id', 'received_by');
    }
}
