<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuAccess;
use App\Models\SubMenu;
use App\Models\SubMenuAccess;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menu = new Menu();
        $menu->order = 2;
        $menu->name = 'Report';
        $menu->route = 'reports';
        $menu->icon = 'ti ti-report';
        $menu->type = 'menu';
        $menu->active = 1;
        $menu->save();
        $menuId = $menu->id;

        // Access
        $accessAllowed = [1, 2];
        foreach ($accessAllowed as $access_id) {
            MenuAccess::create([
                'menu_id' => $menuId,
                'access_id' => $access_id,
            ]);
        }

        $subMenus = [
            [
                'order' => 1,
                'name' => 'Absensi',
                'route' => 'reports.presences',
            ],
            [
                'order' => 2,
                'name' => 'Teams',
                'route' => 'reports.teams',
            ],
            [
                'order' => 3,
                'name' => 'Penjualan',
                'route' => 'reports.sales',
            ],
            [
                'order' => 4,
                'name' => 'Keuangan',
                'route' => 'reports.finance',
            ],
        ];

        foreach ($subMenus as $subMenu) {
            $sub = new SubMenu();
            $sub->name = $subMenu['name'];
            $sub->route = $subMenu['route'];
            $sub->menu_id = $menuId;
            $sub->order = $subMenu['order'];
            $sub->active = true;
            $sub->save();
            $subId = $sub->id;
            foreach ($accessAllowed as $access_id) {
                $subAccess = new SubMenuAccess();
                $subAccess->sub_menu_id = $subId;
                $subAccess->access_id = $access_id;
                $subAccess->save();
            }
        }
    }
}
