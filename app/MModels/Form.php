<?php

namespace App\MModels;

use Jenssegers\Mongodb\Eloquent\Model as Model;
use Jenssegers\Mongodb\Eloquent\HybridRelations;

class Form extends Model
{
    use HybridRelations;

    public $connection = 'mongodb';
    protected $collection = 'forms';
    protected static $unguarded = true;

    public function containers()
    {
        return $this->hasMany(Container::class);
    }
}
