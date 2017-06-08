<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationStepForms extends Model
{
    public $primaryKey = 'application_step_id';
    public $fillable = ['application_step_id', 'mform_id', 'form_templates_id'];
    public $timestamps = false;

    public function Ftest()
    {
        dd('ftest');
    }
}
