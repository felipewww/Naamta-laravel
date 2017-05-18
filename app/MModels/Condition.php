<?php

namespace App\MModels;

use Jenssegers\Mongodb\Eloquent\Model as Model;
use Jenssegers\Mongodb\Eloquent\HybridRelations;

class Condition extends Model
{
    use HybridRelations;
    public $connection = 'mongodb';
    protected $collection = 'conditions';
    protected static $unguarded = true;

    public function rule()
    {
        return $this->belongsTo(Rule::class);
    }
}



