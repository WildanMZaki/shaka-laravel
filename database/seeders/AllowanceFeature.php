<?php

namespace Database\Seeders;

use Database\Seeders\AllowanceFeature\MonthlyNominal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AllowanceFeature extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            MonthlyNominal::class,
        ]);
    }
}
