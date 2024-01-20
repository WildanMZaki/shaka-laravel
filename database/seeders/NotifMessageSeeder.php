<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotifMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\NotificationMessage::create([
            'name' => 'Kasbon Disetujui',
            'message' => 'Selamat {time} {name}, selamat pengajuan kasbon kamu sebesar {nominal_kasbon} telah diterima',
        ]);
    }
}
