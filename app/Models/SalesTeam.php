<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SalesTeam extends Pivot
{
    use HasFactory;

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function teamLeader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }
}
