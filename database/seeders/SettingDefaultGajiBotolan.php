<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingDefaultGajiBotolan extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Settings::create([
            'rule' => 'Default Gaji Botolan',
            'value' => 3000,
            'type' => 'int',
            'tag' => '',
        ]);
    }
}
