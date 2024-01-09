<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenusSeeder extends Seeder
{
    protected $table;
    public function __construct()
    {
        $this->table = 'menus';
    }

    public function run(): void
    {
        $menus = [
            [
                'id' => 1,
                'order' => 1,
                'name' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'mdi mdi-home-outline',
                'type' => 'menu',
                'active' => 1,
            ],
            [
                'id' => 2,
                'order' => 100,
                'name' => 'Setting',
                'route' => 'setting*',
                'icon' => 'ti ti-settings',
                'type' => 'menu',
                'active' => 1,
            ],
            [
                'id' => 3,
                'order' => 2,
                'name' => 'Features',
                'route' => '',
                'icon' => '',
                'type' => 'separator',
                'active' => 1,
            ],
            [
                'id' => 4,
                'order' => 3,
                'name' => 'Data Barang',
                'route' => 'product*',
                'icon' => 'ti ti-bottle',
                'type' => 'menu',
                'active' => 1,
            ],
            [
                'id' => 5,
                'order' => 4,
                'name' => 'Karyawan',
                'route' => 'employee*',
                'icon' => 'mdi mdi-account-group-outline',
                'type' => 'menu',
                'active' => 1,
            ],
        ];

        DB::table($this->table)->insert($menus);
    }
}
