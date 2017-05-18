<?php

namespace App\MModels;

use Jenssegers\Mongodb\Eloquent\Model as Model;
use Jenssegers\Mongodb\Eloquent\HybridRelations;

class Comment extends Model
{
    use HybridRelations;
    
    public $connection = 'mongodb';
    protected $collection = 'comments';
    protected static $unguarded = true;

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}




