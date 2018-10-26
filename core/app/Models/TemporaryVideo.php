<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemporaryVideo extends Model
{
    protected $fillable = ['user_id', 'video_id', 'reference', 'movie_id'];
}
