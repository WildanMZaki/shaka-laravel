<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IntensifSettingsSeeder1 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Settings::create([
            'rule' => 'Target Jual Harian SPG Freelancer',
            'value' => 30,
            'type' => 'int',
        ]);
    }
}
