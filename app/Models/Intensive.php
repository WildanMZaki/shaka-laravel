<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intensive extends Model
{
    use HasFactory;

    protected $fillable = ['access_id', 'sales_qty', 'intensive', 'type', 'period'];
}
