<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    public function restocks()
    {
        return $this->hasMany(Restock::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    protected $appends = ['stock', 'sold'];

    public function getStockAttribute()
    {
        $totalRestocks = $this->restocks()->sum('qty');
        $totalSales = $this->sales()->sum('qty');
        return $totalRestocks - $totalSales;
    }
    public function getSoldAttribute()
    {
        return $this->sales()->sum('qty');
    }

    public function scopeWithPositiveStock($query)
    {
        return
            $query->whereHas('restocks', function ($query) {
                $query->whereNull('restocks.deleted_at');
            })
            ->where(function ($query) {
                $query->whereRaw('
                    (select sum(qty) from restocks where restocks.product_id = products.id and restocks.deleted_at is null) - 
                    (select coalesce(sum(qty), 0) from sales where sales.product_id = products.id and sales.deleted_at is null) > 0
                ');
            });
    }
}
