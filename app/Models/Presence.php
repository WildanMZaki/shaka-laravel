<?php

namespace App\Models;

use App\Helpers\Fun;
use App\Helpers\Muwiza;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presence extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'date', 'entry_at', 'photo', 'status', 'flag', 'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public static function workDayFrom(?string $mondayDate = null, $lastDay = 'Sabtu')
    {
        $thisWeek = Fun::period($mondayDate ?? Muwiza::firstMonday(), $lastDay);
        $workDay = Presence::whereBetween('date', $thisWeek)->groupBy('date')->pluck('date')->toArray();
        return $workDay;
    }

    // Yang dimiliki user selama seminggu
    // $workDayDates bisa digenerate dengan fungsi workDayFrom di atas, tentukan saja tanggal hari senin sampai hari apa
    // Defaultnya data kehadiran yang dimiliki di minggu ini tentunya
    public static function hadBy($user_id, ?array $workDayDates = null)
    {
        $workDay = $workDayDates ?? self::workDayFrom();
        $presencesQuery = Presence::where('user_id', $user_id)
            ->whereIn('status', ['approved', 'pending'])
            ->whereIn('date', $workDay);

        $presences = $presencesQuery->get(['date', 'flag']);
        $totalHadir = $presences->where('flag', 'hadir')->count();
        $totalIzin = $presences->whereIn('flag', ['izin', 'sakit'])->count();
        $tanggalHadir = $presences->pluck('date')->toArray();
        $tanggalTidakHadir = array_diff($workDay, $tanggalHadir);

        return (object)[
            'perfect' => count($tanggalTidakHadir) == 0,
            'totalHadir' => $totalHadir,
            'totalIzin' => $totalIzin,
            'tanggalTidakHadir' => $tanggalTidakHadir,
            'presences' => $presences,
        ];
    }
}
