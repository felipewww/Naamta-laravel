<?php

namespace App\MModels;

use Jenssegers\Mongodb\Eloquent\Model as Model;
use Jenssegers\Mongodb\Eloquent\HybridRelations;

class Setting extends Model
{
    use HybridRelations;

    public $connection = 'mongodb';
    protected $collection = 'settings';
    protected static $unguarded = true;

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
    public function rule()
    {
        return $this->hasOne(Rule::class);
    }
}
