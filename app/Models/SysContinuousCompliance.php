<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SysContinuousCompliance extends Model
{
    public $table = 'sys_continuous_compliance';
    public $fillable = ['application_id', 'form_template_id','interval', 'times', 'times_executed'];

    public function form()
    {
        return $this->belongsTo(FormTemplate::class, 'form_template_id', 'id');
    }
}
