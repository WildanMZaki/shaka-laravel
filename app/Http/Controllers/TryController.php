<?php

namespace App\Http\Controllers;

use App\Helpers\Muwiza;
use App\Helpers\MuwizaTable;
use App\Models\Menu;
use App\Models\Notification;
use App\Models\Presence;
use App\Models\Product;
use App\Models\Restock;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;

class TryController extends Controller
{
    public function debug()
    {

        // $result = $this->seeMuizaTable();
        // $tbl = new MuwizaTable();
        $result = $this->seePresences();

        // echo $result;
        return response()->json($result);
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
