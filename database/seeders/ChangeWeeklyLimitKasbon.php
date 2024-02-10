<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChangeWeeklyLimitKasbon extends Seeder
{
    public function run(): void
    {
        Settings::where('rule', 'Limit Kasbon')->update(['value' => 200000]);
    }
}
