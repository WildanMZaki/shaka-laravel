<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingTotalHariKerja extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Settings::create([
            'rule' => 'Jumlah Hari Kerja',
            'value' => 6,
            'type' => 'int',
            'tag' => '',
        ]);
    }
}
