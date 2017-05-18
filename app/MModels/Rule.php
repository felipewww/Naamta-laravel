<?php

namespace App\MModels;

use Jenssegers\Mongodb\Eloquent\Model as Model;
use Jenssegers\Mongodb\Eloquent\HybridRelations;

class Rule extends Model
{
    use HybridRelations;
    public $connection = 'mongodb';
    protected $collection = 'rules';
    protected static $unguarded = true;

    public function setting()
    {
        return $this->belongsTo(Setting::class);
    }

    public function conditions()
    {
        return $this->hasMany(Condition::class);
    }
}



