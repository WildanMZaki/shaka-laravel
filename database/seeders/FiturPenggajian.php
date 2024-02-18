<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FiturPenggajian extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            PenggajianMenuSeeder::class,
            InsentifsSeeder::class,
            SallariesSeeder::class,
            SettingTotalHariKerja::class,
            SettingDefaultGajiBotolan::class,
        ]);
    }
}
