<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesTeam extends Pivot
{
    use HasFactory, SoftDeletes;
    protected $table = 'sales_teams';

    protected $fillable = [
        'leader_id', 'sales_id', 'created_at'
    ];
}
