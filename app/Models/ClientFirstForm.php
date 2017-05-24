<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientFirstForm extends Model
{
    public $primaryKey = 'client_id';
    public $fillable = ['first_field','second_field','third_field','client_id'];

    
}
