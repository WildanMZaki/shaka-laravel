<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Muwiza;
use App\Http\Controllers\Controller;
use App\Models\Insentif;
use App\Models\Settings;
use Illuminate\Http\Request;

class SallaryRuleController extends Controller
{
    private $req;

    public function index()
    {
        $generalRules = ['Target Jual Harian SPG Freelancer', 'Default Gaji Botolan', 'Nominal BPJS Bulanan'];
        $general = [];
        foreach ($generalRules as $rule) {
            $general[$rule] = Settings::of($rule);
        }
        $data['general'] = $general;
        $data['daily_insentives'] = Insentif::with('access')->where('period', 'daily')->orderBy('sales_qty', 'asc')->get();
        $data['weekly_insentives'] = Insentif::with('access')->where('period', 'weekly')->orderBy('sales_qty', 'asc')->get();
        $data['monthly_insentives'] = Insentif::with('access')->where('period', 'monthly')->orderBy('sales_qty', 'asc')->get();
        return view('admin.sallary-rule.index', $data);
    }

    private function response($msg)
    {
        $insentives = Insentif::with('access')
            ->where('period', $this->req->period)
            ->where('access_id', $this->req->access_id)
            ->orderBy('sales_qty', 'asc')
            ->get()
            ->map(function ($ins) {
                $insentive = $ins;
                $insentive->sales_qty = Muwiza::ribuan($ins->sales_qty);
                $insentive->insentive = $ins->type == 'money' ? Muwiza::rupiah($ins->insentive) : $ins->insentive;
                return $insentive;
            });

        return response()->json([
            'message' => $msg,
            'data' => $insentives,
        ]);
    }

    public function store_insentive(Request $request)
    {
        // Note penting request type ini harus ada untuk memastikan fungsionalitas
        if (!$request->type) {
            return response()->json([
                'message' => 'Tipe insentif tidak diketahui',
            ], 400);
        }

        $ins = $request->type == 'thing' ? 'insentive_thing' : 'insentive';
        $ruleIns = 'required';
        if ($request->type == 'money') {
            $ruleIns .= '|numeric';
        }

        $rules = [
            'sales_qty' => 'required|numeric',
            $ins => $ruleIns,
        ];
        $messages = [
            'sales_qty.required' => 'Kuantitas penjualan harus diisi',
            'sales_qty.numeric' => 'Kuantitas penjualan harus berupa angka',
            "$ins.required" => 'Insentif harus diisi',
            'insentive.numeric' => 'Nilai insentif harus berupa angka',
        ];
        $request->validate($rules, $messages);

        $insVal = $request->type == 'money' ? Muwiza::validInt($request->insentive) : $request->insentive_thing;

        $insentive = new Insentif();
        $insentive->sales_qty = $request->sales_qty;
        $insentive->access_id = $request->access_id;
        $insentive->period = $request->period;
        $insentive->type = $request->type;
        $insentive->insentive = $insVal;
        $insentive->save();

        $this->req = $request;
        return $this->response('Insentif berhasil ditambahkan');
    }

    public function update_insentive(Request $request)
    {
        if (!$request->id) return response()->json([
            'message' => 'Insentif tidak ditemukan',
        ]);

        $insentive = Insentif::find($request->id);

        if (!$insentive) return response()->json([
            'message' => 'Insentif tidak ditemukan',
        ]);

        $ins = $insentive->type == 'thing' ? 'edit_insentive_thing' : 'edit_insentive';
        $ruleIns = 'required';
        if ($insentive->type == 'money') {
            $ruleIns .= '|numeric';
        }

        $rules = [
            'edit_sales_qty' => 'required|numeric',
            $ins => $ruleIns,
        ];
        $messages = [
            'edit_sales_qty.required' => 'Kuantitas penjualan harus diisi',
            'edit_sales_qty.numeric' => 'Kuantitas penjualan harus berupa angka',
            "$ins.required" => 'Insentif harus diisi',
            'edit_insentive.numeric' => 'Nilai insentif harus berupa angka',
        ];
        $request->validate($rules, $messages);

        $insVal = $insentive->type == 'money' ? Muwiza::validInt($request->edit_insentive) : $request->edit_insentive_thing;

        $insentive->sales_qty = $request->edit_sales_qty;
        $insentive->insentive = $insVal;
        $insentive->save();

        $data = new \stdClass();
        $data->access_id = $insentive->access_id;
        $data->period = $insentive->period;
        $this->req = $data;

        return $this->response('Insentif berhasil diperbarui');
    }

    public function delete_insentive($id)
    {
        $insentive = Insentif::find($id);
        if (!$insentive) return response()->json([
            'message' => 'Insentif tidak ditemukan',
        ], 400);

        $data = new \stdClass();
        $data->access_id = $insentive->access_id;
        $data->period = $insentive->period;
        $this->req = $data;
        $insentive->delete();

        return $this->response('Insentif berhasil dihapus');
    }
}
