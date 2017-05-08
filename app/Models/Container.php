<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Container extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     * 
     */
    protected $fillable = [
        'name', 'form_template_id', 'config'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function formTemplate()
    {
        return $this->hasOne('App\Models\FormTemplate');
    }

    public function fields()
    {
        return $this->hasMany('App\Models\Field');
    }
}
