<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Step extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'responsible', 'previous_step', 'status', 'morphs_from', 'ordination'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];


    public function usesEmails()
    {
        return $this->hasMany(UsesEmail::class);
    }

    public function userTypes()
    {
        return $this->belongsToMany(UserType::class, 'uses_emails', 'step_id', 'received_by');
    }
}
