<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id', 'user_id', 'qty', 'modal_item', 'modal', 'price_item', 'total', 'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
