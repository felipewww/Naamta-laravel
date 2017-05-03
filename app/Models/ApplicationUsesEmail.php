<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationUsesEmail extends Model
{
    public $table = 'application_uses_emails';
    public $fillable = ['id', 'email_id', 'received_by', 'application_step_id', 'send_when'];

    public function userTypes()
    {
        return $this->hasOne(ApplicationUserTypes::class, 'id','received_by');
    }

    public function template()
    {
        return $this->hasOne(EmailTemplate::class, 'id', 'email_id');
    }

    public function receivedBy()
    {
        return $this->hasOne(ApplicationUserTypes::class, 'id', 'received_by');
    }
}
