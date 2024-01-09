<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = ['rule', 'value'];

    public static function of(string $rule): int
    {
        $defaultSettings = [
            'Default Harga Jual' => 10000,
        ];
        return self::where('rule', $rule)->value('value') ?? $defaultSettings[$rule] ?? null;
    }
}
