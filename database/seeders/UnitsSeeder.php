<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsSeeder extends Seeder
{
    protected $table;
    public function __construct()
    {
        $this->table = 'units';
    }

    public function run(): void
    {
        DB::table($this->table)->insert([
            'id' => 1,
            'name' => 'Botol',
            'qty' => 1,
        ]);
        DB::table($this->table)->insert([
            'id' => 2,
            'name' => 'Karton',
            'qty' => 24,
        ]);
    }
}
