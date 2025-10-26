<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'Transactions';
    protected $fillable = [
        'ref',
        'user_id',
        'qty',
        'total',
        'status'
    ];
}
