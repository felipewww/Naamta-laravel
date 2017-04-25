<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'form_template_id', 'config'
    ];

    public function formTemplate()
    {
        return $this->hasOne('App\Models\FormTemplate');
    }

    public function fields()
    {
        return $this->hasMany('App\Models\Field');
    }
}
