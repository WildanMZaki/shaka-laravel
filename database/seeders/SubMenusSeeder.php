<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubMenusSeeder extends Seeder
{
    protected $table;
    public function __construct()
    {
        $this->table = 'sub_menus';
    }

    public function run(): void
    {
        /** Menus
         * 1. Dashboard
         * 2. Setting
         * 3. Features (separator)
         * 4. Data Barang
         * 5. Karyawan
         */
        $subMenus = [
            [
                'order' => 1,
                'menu_id' => 2,
                'name' => 'Menu',
                'route' => 'settings.menus',
                'active' => 1,
            ], // 1
            [
                'order' => 2,
                'menu_id' => 2,
                'name' => 'Sub Menu',
                'route' => 'settings.sub_menus',
                'active' => 1,
            ],
            [
                'order' => 2,
                'menu_id' => 2,
                'name' => 'Akses',
                'route' => 'settings.access',
                'active' => 1,
            ], //3
            [
                'order' => 1,
                'menu_id' => 4,
                'name' => 'Daftar Barang',
                'route' => 'products',
                'active' => 1,
            ],
            [
                'order' => 2,
                'menu_id' => 4,
                'name' => 'Restock Barang',
                'route' => 'products.restocks.list',
                'active' => 1,
            ], // 5
            [
                'order' => 1,
                'menu_id' => 5,
                'name' => 'Daftar Karyawan',
                'route' => 'employees',
                'active' => 1,
            ],
        ];

        $id = 1;
        foreach ($subMenus as $i => $subMenu) {
            $subMenus[$i]['id'] = $id;
            $id++;
        }

        \Illuminate\Support\Facades\DB::table($this->table)->insert($subMenus);
    }
}
