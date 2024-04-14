<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = ['rule', 'value', 'type', 'tag'];

    public static function of(string $rule)
    {
        $rule = trim($rule);
        $defaultSettings = [
            'Default Harga Jual' => 10000,
            'Limit Kasbon' => 400000,
            'Auto Konfirmasi Absensi' => 0,
        ];
        $value = self::where('rule', $rule)->value('value') ?? $defaultSettings[$rule] ?? null;
        $type = self::where('rule', $rule)->value('type');

        switch ($type) {
            case 'int':
                $result = intval($value);
                break;
            case 'bool':
                $result = $value == 1;
                break;
            case 'json':
                $result = json_decode($value);
                break;

            default:
                $result = $value;
                break;
        }
        return $result;
    }
}
