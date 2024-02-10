<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kasbon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'nominal', 'note', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public static function of($user_id, $withHistory = false): int|object
    {
        $now = Carbon::now();
        $startOfWeek = $now->startOfWeek(Carbon::MONDAY);

        $kasbons = self::where('user_id', $user_id)
            ->where('created_at', '>=', $startOfWeek)
            ->get();
        $totalThisWeek = $kasbons->sum('nominal');
        $limitThisWeek = Settings::of('Limit Kasbon');
        $left = $limitThisWeek - $totalThisWeek;
        if ($withHistory) {
            return (object)[
                'totalThisWeek' => $totalThisWeek,
                'left' => $left,
                'history' => $kasbons,
            ];
        }
        return $left;
    }
}
