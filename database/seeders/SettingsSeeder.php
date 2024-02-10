<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Settings::create([
            'rule' => 'Default Harga Jual',
            'value' => 10000,
            'type' => 'int',
        ]);

        \App\Models\Settings::create([
            'rule' => 'Limit Kasbon',
            'value' => 200000,
            'type' => 'int',
        ]);

        \App\Models\Settings::create([
            'rule' => 'Auto Konfirmasi Absensi',
            'value' => 0,
            'type' => 'bool',
        ]);
    }
}
