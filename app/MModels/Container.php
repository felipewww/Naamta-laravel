<?php

namespace App\MModels;

use Jenssegers\Mongodb\Eloquent\Model as Model;
use Jenssegers\Mongodb\Eloquent\HybridRelations;

class Container extends Model
{
    use HybridRelations;

    public $connection = 'mongodb';
    protected $collection = 'containers';
    protected static $unguarded = true;

    public function config()
    {
        return $this->hasOne(Config::class);
    }

    public function fields()
    {
        return $this->hasMany(Field::class);
    }

    public function forms()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }
}
