<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccessesSeeder extends Seeder
{
    protected $table;
    public function __construct()
    {
        $this->table = 'accesses';
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table($this->table)->insert([
            'id' => 1,
            'name' => 'Developer',
        ]);
        DB::table($this->table)->insert([
            'id' => 2,
            'name' => 'Administrator',
        ]);
        DB::table($this->table)->insert([
            'id' => 3,
            'name' => 'Team Leader',
            'editable' => true,
        ]);
        DB::table($this->table)->insert([
            'id' => 4,
            'name' => 'Sales',
            'editable' => true,
        ]);
    }
}
