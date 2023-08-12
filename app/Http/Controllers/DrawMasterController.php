<?php

namespace App\Http\Controllers;

use App\Http\Resources\DrawMasterResource;
use App\Models\DrawMaster;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class DrawMasterController extends Controller
{

    public function index()
    {
        $result = DrawMaster::get();
        return response()->json(['success'=>1,'data'=>DrawMasterResource::collection($result)], 200,[],JSON_NUMERIC_CHECK);
    }

    public function reverseDraw($id){
        $today= Carbon::today()->format('Y-m-d');
        DB::select("delete from result_masters where draw_master_id > ".$id." and date(created_at) = '".$today."'");
        DB::select("update draw_masters set active = 0");
        DB::select("update draw_masters set active = 0 where id = ".$id);
        DB::select("update next_game_draws set last_draw_id = ".$id);
        DB::select("update next_game_draws set next_draw_id = ".($id+1));
        return response()->json(['success'=>1, "message" => "Draw id ".$id." activated"], 200,[],JSON_NUMERIC_CHECK);
    }

    public function get_draw_time_by_game_id($id)
    {
        $result = DrawMaster::whereGameId($id)->get();
        return response()->json(['success'=>1,'data'=>DrawMasterResource::collection($result)], 200,[],JSON_NUMERIC_CHECK);
    }

    public function get_incomplete_games_by_date($id){
        $test = Carbon::today();
        $result = DrawMaster::whereDoesnthave('result_masters', function($q) use ($test) {
            $q->where('game_date', '=', $test);
        })
//            ->whereDoesnthave('manual_results', function($q) use ($test) {
//            $q->where(DB::raw('date(created_at)'), '=', $test);
//        })
            ->whereGameId($id)
            ->get();
        return response()->json(['success'=>1,'data'=>DrawMasterResource::collection($result)], 200,[],JSON_NUMERIC_CHECK);
    }

    public function getActiveDraw()
    {
        $result = DrawMaster::where('active',1)->first();
        if(!empty($result)){
            return response()->json(['success'=>1,'data'=> new DrawMasterResource($result)], 200,[],JSON_NUMERIC_CHECK);
        }else{
            return response()->json(['success'=>1,'data'=> null], 200,[],JSON_NUMERIC_CHECK);
        }
    }

    public function getGameActiveDraw($id)
    {
        $result = DrawMaster::where('active',1)->whereGameId($id)->first();
        if(!empty($result)){
            return response()->json(['success'=>1,'data'=> new DrawMasterResource($result)], 200,[],JSON_NUMERIC_CHECK);
        }else{
            return response()->json(['success'=>1,'data'=> null], 200,[],JSON_NUMERIC_CHECK);
        }
    }

    public function setActiveDraw(){

    }


}
