<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerSallary extends Model
{
    use HasFactory;

    protected $fillable = ['sales_qty', 'intensive'];
}
