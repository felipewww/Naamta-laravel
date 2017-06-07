<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContinuousCompliance extends Model
{
    public $table = 'continuous_compliance';
    public $fillable = ['form_template_id', 'application_id'];

    public function form()
    {
        return $this->belongsTo(FormTemplate::class, 'form_template_id', 'id');
    }
}
