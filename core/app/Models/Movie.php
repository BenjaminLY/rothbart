<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    public function videos()
    {
        return $this->hasMany('App\Models\Video');
    }
}
