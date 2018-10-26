<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
       'user_id', 'plan_id', 'movie_id', 'status', 'transaction_id', 'paid_at', 'reference'
    ];
}
