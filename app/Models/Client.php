<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id', 'company', 'user_id'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function application()
    {
        return $this->hasOne('App\Models\Application', 'client_id', 'id');
    }

    public function firstForm()
    {
        return $this->hasOne(ClientFirstForm::class);
//        return $this->belongsTo(ClientFirstForm::class);
    }
}
