<?php

namespace App\MModels;

use Jenssegers\Mongodb\Eloquent\Model as Model;
use Jenssegers\Mongodb\Eloquent\HybridRelations;

class Field extends Model
{
    use HybridRelations;
    public $connection = 'mongodb';
    protected $collection = 'fields';
    protected static $unguarded = true;

    public function container()
    {
        return $this->belongsTo(Container::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function setting()
    {
        return $this->hasOne(Setting::class);
    }
}
