<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SalesTeam extends Pivot
{
    use HasFactory;
    protected $table = 'sales_teams';
}
