<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubMenuAccess extends Model
{
    use HasFactory;

    public function subMenu()
    {
        return $this->belongsTo(SubMenu::class);
    }
}
