<?php

namespace App\Models;

use App\Helpers\Fun;
use App\Helpers\Muwiza;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id', 'user_id', 'qty', 'modal_item', 'modal', 'price_item', 'total', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    // Note, dateTime itu tanggalnya, jadi hari apapun itu akan dihitungkan dari hari senin pertamanya
    public static function fromSPG($user_id, ?string $dateTime = null)
    {
        $period = Fun::periodWithHour(date('Y-m-d', strtotime(Muwiza::firstMonday($dateTime))));

        $salesData = self::where('user_id', $user_id)
            ->whereIn('status', ['done', 'processed'])
            ->whereBetween('created_at', $period)
            ->selectRaw('DATE(created_at) as date, SUM(qty) as total_qty')
            ->groupBy('date')
            ->get();

        return $salesData;
    }

    public static function fromLeader($leaderId, ?string $dateTime = null)
    {
        $leader = User::find($leaderId);
        $periodDates = Presence::workDayFrom(date('Y-m-d', strtotime(Muwiza::firstMonday($dateTime))));
        $leaderSales = [];
        foreach ($periodDates as $date) {
            $sales = $leader->sales()->whereDate('sales_teams.created_at', $date)->get();
            $qty = 0;
            foreach ($sales as $spg) {
                $selling = $spg->selling()->whereDate('created_at', $date)->sum('qty');
                $qty += $selling;
            }
            $leaderSales[] = (object)[
                'date' => $date,
                'total_qty' => $qty,
            ];
        }
        return $leaderSales;
    }

    public static function totalSales($salesData)
    {
        $total = 0;
        foreach ($salesData as $sale) {
            $total += $sale->total_qty;
        }
        return $total;
    }
}
