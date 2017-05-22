<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationStepApprovals extends Model
{
    public $fillable = ['application_step_id', 'mapproval_id','approval_id'];
    public $primaryKey = 'application_step_id';
    public $timestamps = false;
}
