<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsesEmail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email_id', 'received_by', 'send_when', 'step_id'
    ];

    public function template()
    {
        return $this->hasOne(EmailTemplate::class, 'id', 'email_id');
    }
}
