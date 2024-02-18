<?php

namespace App\Http\Controllers;

use App\Helpers\Fun;
use App\Helpers\Muwiza;
use App\Helpers\MuwizaTable;
use App\Models\Insentif;
use App\Models\Kasbon;
use App\Models\Menu;
use App\Models\Notification;
use App\Models\Presence;
use App\Models\Product;
use App\Models\Restock;
use App\Models\Sale;
use App\Models\Sallary;
use App\Models\Settings;
use App\Models\User;
use App\Models\WeeklySallary;
use Illuminate\Http\Request;

class TryController extends Controller
{
    public function debug()
    {

        // $result = $this->seeSaleWithKeep(4);
        // $result = $this->seeSallary();
        // $result = $this->seeLeaderSelling();
        // $result = $this->seeInsentif();
        $result = $this->seeTotalGivenSallariesThisWeek();
        return response()->json($result);
    }

    private function seeTotalGivenSallariesThisWeek()
    {
        return Fun::getGivenSallaries();
    }

    private function seeInsentif()
    {
        return (object)[
            'insentive' => Insentif::detailFor(6, Sale::fromLeader(6)),
            'presence' => Presence::hadBy(6),
        ];
    }

    private function seeSallary()
    {
        $user = User::find(6);
        return WeeklySallary::currentWeekFrom($user);
    }

    private function seeSaleWithKeep($user_id, ?string $dateTime = null)
    {
        $keepData = Kasbon::keepFrom($user_id, $dateTime);

        $rangeDate = Muwiza::mondayUntilNow();
        $kasbons = Kasbon::where('user_id', $user_id)
            ->where('type', 'keep')
            ->whereBetween('created_at', $rangeDate)
            ->get();

        $firstKeepDate = $kasbons->sortBy('created_at')->first()->created_at;
        $firstKeepTime = strtotime(date($firstKeepDate));

        $salesData = Sale::fromSPG($user_id);

        $target = Settings::of('Target Jual Harian SPG Freelancer');
        $defaultSalePrice = Settings::of('Default Harga Jual');

        // Qty yang lebih dari target:
        $qtyPass = 0;
        foreach ($salesData as $i => $sale) {
            // $keep = $this->getQtyKeepByDate($keepData, $sale->date);
            $keepQty = $this->getQtyKeepByDate($keepData, $sale->date);
            $realQty = $sale->total_qty - $keepQty;
            $salesData[$i]->realQty = $realQty;
            $salesData[$i]->keep = $keepQty * $defaultSalePrice;
            if ($realQty > $target && strtotime(date($sale->date)) > $firstKeepTime) {
                $qtyPass += ($realQty - $target);
            }
        }

        $nominalLebih = $qtyPass * $defaultSalePrice;

        return ['salesData' => $salesData, 'keep' => $keepData, 'nominal_lebih' => $nominalLebih, 'qty_lebih' => $qtyPass];
    }

    private function getQtyKeepByDate($data, $date)
    {
        foreach ($data as $item) {
            if ($item['date'] === $date) {
                return $item['qty_keep'];
            }
        }
        // Return 0 if no match is found
        return 0;
    }

    public function seeLeaderSelling()
    {
        return Sale::fromLeader(11);
    }

    private function seeSalesDetail()
    {
        return Sale::fromSPG(17);
    }

    private function seePeriods()
    {
        // $thisWeek = Muwiza::mondayUntilNow();
        // $workDay = Presence::workDayThisWeek();

        // $presencesDetail = Presence::hadBy(8);
        $user = User::find(8);
        $presencesDetail = $user->presenceThisWeek();
        return $presencesDetail;
    }

    private function seeSaleAfterKasbon()
    {
        // Check if the user has kasbons
        $user_id = 23;
        $rangeDate = Muwiza::mondayUntilNow();
        $kasbons = Kasbon::where('user_id', $user_id)
            ->where('type', 'keep')
            ->where('status', 'approved')
            ->whereBetween('created_at', $rangeDate)
            ->get();

        // If there are no kasbons, stop the job
        if ($kasbons->isEmpty()) {
            return;
        }

        $firstKasbonDate = $kasbons->sortBy('created_at')->first()->created_at;

        $salesData = Sale::where('user_id', $user_id)
            ->where('created_at', '>', $firstKasbonDate)
            ->selectRaw('DATE(created_at) as date, SUM(qty) as total_qty, SUM(total) as total_income')
            ->groupBy('date')
            ->get();

        $target = Settings::of('Target Jual Harian SPG Freelancer');

        // Qty yang lebih dari target:
        $qtyPass = 0;
        foreach ($salesData as $sale) {
            if ($sale->total_qty > $target) {
                $qtyPass += ($sale->total_qty - $target);
            }
        }

        $defaultSalePrice = Settings::of('Default Harga Jual');
        $nominalLebih = $qtyPass * $defaultSalePrice;

        $kasbonPaid = Kasbon::where('user_id', $user_id)->where('status', 'paid')->where('type', 'keep')->sum('nominal');
        $nominalLebih -= $kasbonPaid;

        foreach ($kasbons as $kasbon) {
            if ($nominalLebih >= $kasbon->nominal) {
                // $kasbon->status = 'paid';
                // $kasbon->save();

                $nominalLebih -= $kasbon->nominal;
            }
        }

        return [$kasbons, $salesData, $qtyPass, $nominalLebih];
    }

    private function seeProducts()
    {
        $data['activeProducts'] = Product::withPositiveStock()->where('active', true)->get(['id', 'merk']);
        return $data;
    }

    function seeLastMonday()
    {
        $start_of_week_monday = date('Y-m-d', strtotime('last Monday', strtotime(date('Y-m-d'))));
        return $start_of_week_monday;
    }


    private function seeEmployees()
    {
        $employees = User::where('access_id', '>', 2)->orderBy('created_at', 'desc')->get();
        $tableEmployees = MuwizaTable::generate($employees, function ($row, $cols) {
            $cols->position = $row->access->name;
            return $cols;
        })->extract(['name', 'phone', 'email', 'nik', 'position', 'photo'])
            ->withoutId()
            ->result();

        return $tableEmployees;
    }

    private function seeActionsGenerated()
    {
        $btns = [
            [
                'selector' => 'btn-edit',
                'datas' => [
                    'id' => 1, 'title' => 'This is title'
                ],
                'classColor' => 'btn-label-warning',
                'textColor' => 'text-warning',
                'tooltip' => 'Edit'
            ],
            [
                'selector' => 'btn-delete',
                'datas' => [
                    'id' => 1, 'title' => 'This is title'
                ],
                'classColor' => 'btn-label-danger',
                'classIcon' => 'ti ti-trash',
                'textColor' => 'text-danger',
                'tooltip' => 'Hapus'
            ],
        ];
        return MuwizaTable::generateActions($btns);
    }

    private function seeMuizaTable()
    {
        $restocks = Restock::orderBy('created_at', 'desc')->get();
        // return
        // MuwizaTable::generate($products, function ($row) {
        //     $col = (object)[];
        //     $col->stock = $row->restocks->sum('qty');
        //     return $col;
        // })
        // ->extract(['merk'])
        // ->col('stock', 'ribuan', function ($stock) {
        //     return  $stock . ' Botol';
        // })
        // ->col('sell_price', 'rupiah')
        // ->col('sold', function ($row) {
        //     return 'dummy';
        // })
        // ->col('status', function ($row) {
        //     return  $row->active ? '<span class="badge bg-label-success">Aktif</span>' : '<span class="badge bg-label-danger">Nonaktif</span>';
        // })
        // ->actions(function ($row) {
        //     $btns = [];
        //     $btns[] = MuwizaTable::$btnsDefault['edit'];
        //     $btns[0]['data'] = [
        //         'id' => $row->id, 'merk' => $row->merk, 'sell_price' => Muwiza::ribuan($row->sell_price),
        //     ];
        //     $btns[] = MuwizaTable::$btnsDefault[$row->active ? 'inactivate' : 'activate'];
        //     $btns[1]['data'] = [
        //         'id' => $row->id
        //     ];
        //     $btns[1]['classIcon'] = $row->active ? 'ti ti-box-off' : 'ti ti-box';
        //     $btns[1]['selector'] = 'btn-active-control';
        //     return $btns;
        // })
        // ->resultHTML();
        // return $restocks;
        // return MuwizaTable::generate($restocks, function ($row, $cols) {
        //     $cols->merk = 'test';
        //     return $cols;
        // })->extract(['merk', 'type'])
        //     ->col('restock_date', 'simpleDate')
        //     ->col('qty', function ($row) {
        //         return $row->qty . ' Botol';
        //     })->col('price_total', 'rupiah')
        //     ->col('expiration_date', 'simpleDate')
        //     ->actions(function ($row) {
        //         $btns = [];
        //         $btns[] = MuwizaTable::$btnsDefault['edit'];
        //         $btns[0]['data'] = [
        //             'id' => $row->id,
        //         ];
        //         $btns[] = MuwizaTable::$btnsDefault[$row->active ? 'inactivate' : 'activate'];
        //         $btns[1]['data'] = [
        //             'id' => $row->id
        //         ];
        //         $btns[1]['classIcon'] = $row->active ? 'ti ti-box-off' : 'ti ti-box';
        //         $btns[1]['selector'] = 'btn-active-control';
        //         return $btns;
        //     })
        //     ->result();
        return MuwizaTable::generate($restocks)
            ->col('price_total', 'rupiah')
            ->col('qty', '{data} Botol', '{data} sekali transaksi')
            // ->col('testArrayFormat', ['{data} Botol', 'qty'])
            ->col('testArrayFormatter', ['simpleDate', 'expiration_date'], function ($formattedValue, $row) {
                return $formattedValue . "({$row->expiration_date})";
            })
            ->col('testCallback', function ($row) {
                return date('M Y', strtotime($row->restock_date));
            })
            ->result();
    }

    private function seeSetting()
    {
        return Settings::of('Default Harga Jual');
    }

    private function seeMenus($accessId)
    {
        // return \App\Models\Menu::whereHas('menuAccesses', function ($query) use ($accessId) {
        //     $query->where('access_id', $accessId);
        // })->with(['subMenus' => function ($query) use ($accessId) {
        //     $query->whereHas('subMenuAccesses', function ($query) use ($accessId) {
        //         $query->where('access_id', $accessId);
        //     });
        // }])->get();
        $menus = \App\Models\Menu::whereHas('menuAccesses', function ($query) use ($accessId) {
            $query->where('access_id', $accessId);
        })
            ->with([
                'subMenus' => function ($query) use ($accessId) {
                    $query->whereHas('subMenuAccesses', function ($query) use ($accessId) {
                        $query->where('access_id', $accessId);
                    })
                        ->where('active', 1) // Only get active submenus
                        ->orderBy('order', 'asc'); // Order submenus by 'order' column
                }
            ])
            ->where('active', 1) // Only get active menus
            ->orderBy('order', 'asc') // Order menus by 'order' column
            ->get();

        $subMenus = [];
        foreach ($menus as $menu) {
            $subMenus[] = $menu->subMenus;
        }
        return $subMenus;
    }

    private function seeTestNotif($user_id)
    {
        return Notification::sendTo($user_id, [
            'Testing', 'Selamat {time}, wahay sia {name}, other data: {plc1}, {plc2}'
        ], [
            'plc1' => 'IAMPLC1',
            'plc2' => 'IAMPLCTWO',
        ]);
    }

    private function seeUserDetail()
    {
        $user = User::find(4);
        return $user->leader;
    }

    private function seePresences()
    {
        // $presences = Presence::with(['user.access', 'user' => function ($query) {
        //     $query->select('id', 'name', 'access_id');
        // }])
        //     ->where('flag', 'hadir')
        //     ->whereDate('date', now())
        //     ->get();
        // return $presences;

        $unpresence = User::whereDoesntHave('presences', function ($query) {
            $query->whereDate('date', now());
            $query->orderBy('entry_at', 'asc');
        })
            ->where('access_id', '>', 2)
            ->get();

        return $unpresence;
    }
}
