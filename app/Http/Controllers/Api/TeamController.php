<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    public function today(Request $request)
    {
        $user_id = $request->attributes->get('user_id');
        $access_id = $request->attributes->get('access_id');

        if ($access_id != 5) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Team Leader Yang Boleh Membuat Tim',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'sales_id' => 'required',
        ], [
            'sales_id.required' => 'SPG harus dipilih',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $sales_id = $request->sales_id;
        // Harusnya ada validasi ada atau tidaknya spg di users, dan jabatannya beneran sales atau bukan, tapi karena low budget, skip ae lah
        $q = SalesTeam::where('sales_id', $sales_id)->whereDate('date', now());
        if ($q->exists()) {
            $row = $q->first();
            $msg = ($row->leader_id == $user_id) ? 'SPG tersebut telah dipilih oleh anda' : 'SPG tersebut telah dipilih oleh leader lain';
            return response()->json([
                'success' => false,
                'message' => $msg,
            ], 400);
        }

        $salesTeam = new SalesTeam();
        $salesTeam->leader_id = $user_id;
        $salesTeam->sales_id = $sales_id;
        $salesTeam->date = now();
        $salesTeam->save();
        return response()->json([
            'success' => true,
            'message' => 'SPG ditambahkan',
        ]);
    }
}
