<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'order', 'name', 'route', 'icon', 'type', 'active',
    ];
    public $timestamps = false;

    public function menuAccesses()
    {
        return $this->hasMany(MenuAccess::class);
    }

    public function subMenus()
    {
        return $this->hasMany(SubMenu::class);
    }
}
