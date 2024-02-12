<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuAccess;
use App\Models\SubMenu;
use App\Models\SubMenuAccess;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PenggajianMenuSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menu
        $menuName = 'Penggajian';
        $menuRoute = 'sallaries*';
        $menuIcon = 'ti ti-coin';

        $lastMenu = Menu::orderBy('id', 'DESC')->first();
        $menu = new Menu();
        $menu->order = $lastMenu->order + 1;
        $menu->name = $menuName;
        $menu->route = $menuRoute;
        $menu->icon = $menuIcon;
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


        $lastSubMenu = SubMenu::orderBy('id', 'DESC')->first();
        $subMenus = [
            [
                'name' => 'Aturan',
                'route' => 'sallaries.rules'
            ],
            [
                'name' => 'Karyawan',
                'route' => 'sallaries.list'
            ],
        ];
        $nextOrder = $lastSubMenu->order + 1;
        foreach ($subMenus as $subMenu) {
            $sub = new SubMenu();
            $sub->name = $subMenu['name'];
            $sub->route = $subMenu['route'];
            $sub->menu_id = $menuId;
            $sub->order = $nextOrder;
            $sub->active = true;
            $sub->save();
            $subId = $sub->id;
            foreach ($accessAllowed as $access_id) {
                $subAccess = new SubMenuAccess();
                $subAccess->sub_menu_id = $subId;
                $subAccess->access_id = $access_id;
                $subAccess->save();
            }
            $nextOrder++;
        }
    }
}
