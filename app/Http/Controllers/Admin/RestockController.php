<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Muwiza;
use App\Helpers\MuwizaTable;
use App\Http\Controllers\Controller;
use App\Models\Restock;
use Illuminate\Http\Request;

class RestockController extends Controller
{
    public function index(Request $request)
    {
        $data['restocks'] = Restock::orderBy('created_at', 'desc')->get();
        $table = $this->generateTable($data['restocks']);
        if ($request->ajax()) {
            $rows = $table->result();
            return response()->json($rows);
        }
        $data['table'] = $table;
        return view('admin.restocks', $data);
    }

    private function generateTable($rowsData): MuwizaTable
    {
        return MuwizaTable::generate($rowsData, function ($row, $cols) {
            $cols->merk = $row->product->deleted_at ? "<span class='text-danger'>{$row->product->merk}</span>" : $row->product->merk;
            return $cols;
        })->extract(['merk', 'type'])
            ->col('restock_date', 'simpleDate')
            ->col('qty', 'ribuan', '{data} Botol')
            ->col('modal', ['rupiah', 'price_total'])
            ->col('expDate', ['simpleDate', 'expiration_date'])
            ->actions(function ($row) {
                $btns = [];
                $btns[] = MuwizaTable::$btnsDefault['edit'];
                $btns[0]['data'] = [
                    'id' => $row->id,
                ];
                $btns[0]['url'] = route('product.restock') . "?id={$row->id}";
                return $btns;
            });
    }

    public function delete(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'message' => 'Invalid request'
        ], 400);

        foreach ($request->id as $id) {
            $restockData = Restock::find($id);
            if ($restockData) {
                $restockData->delete();
            } else {
                return response()->json([
                    'message' => 'Riwayat belanja tidak ditemukan'
                ], 400);
            }
        }

        return response()->json([
            'message' => 'Riwayat belanja berhasil dihapus',
        ]);
    }
}
