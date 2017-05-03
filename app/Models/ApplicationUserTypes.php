<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationUserTypes extends Model
{
    public $fillable = [
        'id', 'slug', 'title', 'status', 'application_id'
    ];

    public $table = 'application_user_types';

    public function usesEmails()
    {
        return $this->hasMany(ApplicationUsesEmail::class, 'received_by');
    }
    
    public function application()
    {
        return $this->hasOne(Application::class, 'id','application_id');
    }
}
