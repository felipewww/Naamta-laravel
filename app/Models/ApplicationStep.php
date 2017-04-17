<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationStep extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'responsible', 'status'
    ];

    public function form()
    {
        return $this->hasOne('App\Models\Form');
    }

    public function screen()
    {
        return $this->hasOne('App\Models\Screen');
    }

    public function step()
    {
        return $this->hasOne('App\Models\Step');
    }

    public function application()
    {
        return $this->hasOne('App\Models\Application');
    }
}
