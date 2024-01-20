<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationMessage extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'message'];

    public static function of(string $name): string
    {
        $defaultMessages = [
            'Kasbon Disetujui' => 'Selamat {time} {name}, selamat pengajuan kasbon kamu sebesar {nominal_kasbon} telah diterima',
        ];
        return self::where('name', $name)->value('message') ?? $defaultMessages[$name] ?? '-';
    }
}
