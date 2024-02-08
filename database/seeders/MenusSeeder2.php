<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenusSeeder2 extends Seeder
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
                'id' => 8,
                'order' => 7,
                'name' => 'Penjualan',
                'route' => 'sales',
                'icon' => 'mdi mdi-trending-up',
                'type' => 'menu',
                'active' => 1,
            ],
            [
                'id' => 9,
                'order' => 8,
                'name' => 'Pengeluaran',
                'route' => 'expenditures',
                'icon' => 'mdi mdi-trending-down',
                'type' => 'menu',
                'active' => 1,
            ],
        ];

        DB::table($this->table)->insert($menus);
    }
}
