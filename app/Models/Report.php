<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id', 'title', 'approval_id', 'form', 'forms_errors'
    ];

    public function approval()
    {
        return $this->hasOne('App\Models\Approval');
    }

    public function history()
    {
        return $this->hasMany('App\Models\HistoryReport');
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
