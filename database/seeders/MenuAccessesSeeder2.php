<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuAccessesSeeder2 extends Seeder
{
    protected $table;
    public function __construct()
    {
        $this->table = 'menu_accesses';
    }

    public function run(): void
    {
        /**
         * Menus:
         * 1. Dashboard
         * 2. Setting
         * 3. Features (separator)
         * 4. Data Barang
         * 5. Karyawan
         * 6. Absensi
         * 7. Kasbon
         * 
         * // New:
         * 8. Penjualan
         * 9. Pengeluarn
         * Access: 1. Developer, 2. Administrator
         */
        $accessIds = (object)[
            'dev' => 1,
            'admin' => 2,
        ];

        $menuIds = [8, 9];
        $menuForAdmins = [8, 9];

        // Aturan: 1. Developer Harus Bisa Akses Semua Menu
        //         2. Batasi Menu Untuk Admin
        $rows = [];
        foreach ($menuIds as $menuId) {
            $rows[] = [
                'access_id' => $accessIds->dev,
                'menu_id' => $menuId,
            ];
            if (in_array($menuId, $menuForAdmins)) {
                $rows[] = [
                    'access_id' => $accessIds->admin,
                    'menu_id' => $menuId,
                ];
            }
        }

        $id = 14;
        foreach ($rows as $i => $row) {
            $rows[$i]['id'] = $id;
            $id++;
        }

        \Illuminate\Support\Facades\DB::table($this->table)->insert($rows);
    }
}