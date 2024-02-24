<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\SubMenu;
use App\Models\SubMenuAccess;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MonthlySallarySubMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menu = Menu::where('name', 'Penggajian')->first();
        $subMenu = new SubMenu();
        $subMenu->order = 3;
        $subMenu->menu_id = $menu->id;
        $subMenu->name = 'Bulanan';
        $subMenu->route = 'sallaries.monthly';
        $subMenu->save();

        $subMenuId = $subMenu->id;

        $subMenuAccess1 = new SubMenuAccess();
        $subMenuAccess1->sub_menu_id = $subMenuId;
        $subMenuAccess1->access_id = 1;
        $subMenuAccess1->save();

        $subMenuAccess2 = new SubMenuAccess();
        $subMenuAccess2->sub_menu_id = $subMenuId;
        $subMenuAccess2->access_id = 2;
        $subMenuAccess2->save();
    }
}
