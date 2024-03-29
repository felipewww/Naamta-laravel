<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Approval extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id', 'title', 'description', 'has_report'
    ];

    /**
     * @Deprecated
     * Don't use this method. It returns all reports, of all clients.
     */
    public function report()
    {
        if($this->has_report)
            return $this->hasMany('App\Models\Report', 'approval_id', 'id');
        return null;
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function Step()
    {
        return $this->hasOne(ApplicationStep::class, 'morphs_id');
    }
}
