<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AccessesSeeder::class,
            UsersTableSeeder::class,
            MenusSeeder::class,
            SubMenusSeeder::class,
            MenuAccesesSeeder::class,
            SubMenuAccesesSeeder::class,
            UnitsSeeder::class,
            SettingsSeeder::class,
            NotifMessageSeeder::class,
        ]);
    }
}
