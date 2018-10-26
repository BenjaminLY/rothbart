<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessRight extends Model
{
    protected $fillable = [
       'user_id', 'movie_id', 'type'
    ];
}
