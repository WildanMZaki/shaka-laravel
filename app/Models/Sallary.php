<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sallary extends Model
{
    use HasFactory;

    protected $fillable = [
        'access_id', 'nominal', 'reducable', 'given', 'rule',
    ];
}
