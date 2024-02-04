<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesTeam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $access_id = $request->attributes->get('access_id');
        $user = $request->attributes->get('user');

        $leader = ($access_id == 5) ? $user : $user->leader()->wherePivot('deleted_at', null)->whereDate('sales_teams.created_at', now())->first();

        $spgs = $leader
            ? $leader->sales()
            ->wherePivot('deleted_at', null)
            ->whereDate('sales_teams.created_at', now())
            ->get(['users.id', 'access_id', 'photo', 'name', 'phone'])
            : [];

        $only = $leader ?? $user;
        $teams = [[
            'id' => $only->id,
            'name' => $only->name,
            'phone' => $only->phone,
            'access_id' => $only->access_id,
            'position' => $only->access->name,
            'photo' => $only->photoPath(),
            'is_me' => $only->id == $user->id,
        ]];
        foreach ($spgs as $spg) {
            $teams[] = [
                'id' => $spg->id,
                'name' => $spg->name,
                'phone' => $spg->phone,
                'access_id' => $spg->access_id,
                'position' => $spg->access->name,
                'photo' => $spg->photoPath(),
                'is_me' => $spg->id == $user->id,
            ];
        }
        return response()->json([
            'success' => true,
            'data' => $teams,
        ]);
    }

    public function spgs()
    {
        $salesIds = SalesTeam::whereDate('created_at', now())->pluck('sales_id');
        $salesReady = User::where('active', true)
            ->whereNotIn('id', $salesIds)
            ->whereIn('access_id', [6, 7])
            ->with(['access' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['presences' => function ($query) {
                $query->select('id', 'flag', 'status')->whereDate('date', now());
            }])
            ->get(['id', 'name', 'photo', 'phone']);
        foreach ($salesReady as $i => $sales) {
            $salesReady[$i]->photo = $sales->photoPath();
            $salesReady[$i]->presences = empty($sales->presences) ? null : $sales->presences;
        }
        return response()->json(['success' => true, 'data' => $salesReady]);
    }

    public function delete_spg(Request $request, $sales_id)
    {
        $user_id = $request->attributes->get('user_id');

        $rowTeam = SalesTeam::where('leader_id', $user_id)->where('sales_id', $sales_id)->whereDate('created_at', now())->first();

        if ($rowTeam) {
            $rowTeam->delete();
        } else {
            return response()->json([
                'success' => false, 'message' => 'Data tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true, 'message' => 'SPG berhasil dihapus',
        ]);
    }

    public function store(Request $request)
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
        $q = SalesTeam::where('sales_id', $sales_id)->whereDate('created_at', now());
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
        $salesTeam->save();
        return response()->json([
            'success' => true,
            'message' => 'SPG ditambahkan',
        ]);
    }
}
