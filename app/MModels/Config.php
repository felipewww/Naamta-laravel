<?php

namespace App\MModels;

use Jenssegers\Mongodb\Eloquent\HybridRelations;
use Jenssegers\Mongodb\Eloquent\Model as Model;

class Config extends Model {
    use HybridRelations;

    public $connection = 'mongodb';
    protected $collection = 'config';
    protected static $unguarded = true;

    public function container()
    {
        return $this->belongsTo(Container::class);
    }
}
