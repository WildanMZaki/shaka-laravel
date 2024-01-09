<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    public function menuAccesses()
    {
        return $this->hasMany(MenuAccess::class);
    }

    public function subMenus()
    {
        return $this->hasMany(SubMenu::class);
    }
}
