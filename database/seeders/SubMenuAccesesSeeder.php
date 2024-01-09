<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubMenuAccesesSeeder extends Seeder
{
    protected $table;
    public function __construct()
    {
        $this->table = 'sub_menu_accesses';
    }

    public function run(): void
    {
        /** Sub Menus:
         * 1. Setting Menu
         * 2. Setting Sub Menu
         * 3. Setting Access
         * 4. Data Barang - Daftar Barang
         * 5. Data Barang - Restock Barang
         * 6. Karyawan - Daftar Karyawan
         */
        $subMenuAccesses = [
            [
                'access_id' => 1,
                'sub_menu_id' => 1,
            ],
            [
                'access_id' => 1,
                'sub_menu_id' => 2,
            ],
            [
                'access_id' => 1,
                'sub_menu_id' => 3,
            ],
            [
                'access_id' => 1,
                'sub_menu_id' => 4,
            ],
            [
                'access_id' => 2,
                'sub_menu_id' => 4,
            ],
            [
                'access_id' => 1,
                'sub_menu_id' => 5,
            ],
            [
                'access_id' => 2,
                'sub_menu_id' => 5,
            ],
            [
                'access_id' => 1,
                'sub_menu_id' => 6,
            ],
            [
                'access_id' => 2,
                'sub_menu_id' => 6,
            ],
            [
                'access_id' => 1,
                'sub_menu_id' => 7,
            ],
            [
                'access_id' => 2,
                'sub_menu_id' => 7,
            ],
        ];

        $id = 1;
        foreach ($subMenuAccesses as $i => $subMenuAccess) {
            $subMenuAccesses[$i]['id'] = $id;
            $id++;
        }

        \Illuminate\Support\Facades\DB::table($this->table)->insert($subMenuAccesses);
    }
}
