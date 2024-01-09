<?php

namespace App\Helpers;

class MuwizaView
{
    public static function getMenu($accessId)
    {
        return \App\Models\Menu::whereHas('menuAccesses', function ($query) use ($accessId) {
            $query->where('access_id', $accessId);
        })
            ->with([
                'subMenus' => function ($query) use ($accessId) {
                    $query->whereHas('subMenuAccesses', function ($query) use ($accessId) {
                        $query->where('access_id', $accessId);
                    })
                        ->where('active', 1) // Only get active submenus
                        ->orderBy('order', 'asc'); // Order submenus by 'order' column
                }
            ])
            ->where('active', 1) // Only get active menus
            ->orderBy('order', 'asc') // Order menus by 'order' column
            ->get();
    }
}
