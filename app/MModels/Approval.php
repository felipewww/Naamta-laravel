<?php

namespace App\MModels;

use Jenssegers\Mongodb\Eloquent\Model as Model;
use Jenssegers\Mongodb\Eloquent\HybridRelations;

class Approval extends Model
{
    use HybridRelations;

    public $connection = 'mongodb';
    protected $collection = 'approvals';
    protected static $unguarded = true;
}
