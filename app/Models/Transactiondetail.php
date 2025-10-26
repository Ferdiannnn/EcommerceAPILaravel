<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactiondetail extends Model
{
    protected $table = 'Transactiondetails';
    protected $fillable = [
        'ref_id',
        'user_id',
        'product_id',
        'qty',
        'total',
    ];
}
