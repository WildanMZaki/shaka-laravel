<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyInsentive extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'weekly_sallaries_id', 'start_date', 'end_date', 'sales_qty', 'insentive', 'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
