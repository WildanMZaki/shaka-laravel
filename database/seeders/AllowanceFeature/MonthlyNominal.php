<?php

namespace Database\Seeders\AllowanceFeature;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MonthlyNominal extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Settings::create([
            'rule' => 'Nominal BPJS Bulanan',
            'value' => 150000,
            'tag' => '',
            'type' => 'int',
        ]);
    }
}
