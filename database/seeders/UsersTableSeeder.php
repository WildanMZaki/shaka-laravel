<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    protected $table;
    public function __construct()
    {
        $this->table = 'users';
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $devPass = env('DEV_PASS', 'DevPass1423');
        $adminPass = env('ADMIN_PASS', 'admin');
        DB::table($this->table)->insert([
            'name' => 'Developer',
            'access_id' => 1,
            'username' => 'dev',
            'phone' => '089619925691',
            'email' => 'wildanmzaki7@gmail.com',
            'email_verified_at' => now(),
            'is_employee' => false,
            'password' => bcrypt($devPass),
        ]);
        DB::table($this->table)->insert([
            'name' => 'Administrator',
            'access_id' => 2,
            'username' => 'admin',
            'phone' => '081234567890',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => bcrypt($adminPass),
        ]);
    }
}
