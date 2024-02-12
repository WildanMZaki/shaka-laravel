<?php

namespace Database\Seeders;

use App\Models\Sallary;
use App\Models\TrainerSallary;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SallariesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sallaries = [
            [
                'access_id' => 5,
                'nominal' => 550000,
            ],
            [
                'access_id' => 6,
                'nominal' => 550000,
                'reducable' => true,
            ],
            [
                'access_id' => 7,
                'nominal' => 550000,
                'reducable' => true,
            ],
        ];
        foreach ($sallaries as $sallary) {
            Sallary::create($sallary);
        }
    }
}
