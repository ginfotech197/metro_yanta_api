<?php

namespace App\Http\Controllers;

use App\Models\DrawMaster;
use App\Models\Game;
use App\Models\GameType;
use App\Models\ManualResult;
use App\Models\NextGameDraw;
use App\Models\NumberCombination;
use App\Models\ResultDetail;
use App\Models\ResultMaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;


class ResultMasterController extends Controller
{
    public function get_results()
    {
//        $result_dates= ResultMaster::distinct()->orderBy('game_date','desc')->pluck('game_date')->take(40);
//
//        $result_array = array();
//        foreach($result_dates as $result_date){
//            $temp_array['date'] = $result_date;
//
//
//
//            $data = DrawMaster::select('result_masters.game_date','draw_masters.end_time','number_combinations.triple_number', 'result_masters.game_id',
//                'number_combinations.visible_triple_number','single_numbers.single_number')
//                ->leftJoin('result_masters', function ($join) use ($result_date) {
//                    $join->on('draw_masters.id','=','result_masters.draw_master_id')
//                        ->where('result_masters.game_date','=', $result_date);
//                })
//                ->leftJoin('number_combinations','result_details.combination_number_id','number_combinations.id')
//                ->leftJoin('single_numbers','number_combinations.single_number_id','single_numbers.id')
//                ->leftJoin('games','result_masters.game_id','games.id')
//                // ->where('games.id','=',$gameId)
//                ->get();
//
//            /*Do Not delete*/
//            /* This is another way to use sub query */
////            $result_query =get_sql_with_bindings(ResultMaster::where('game_date',$result_date));
////            $data1 = DrawMaster::leftJoin(DB::raw("($result_query) as result_masters"),'draw_masters.id','=','result_masters.draw_master_id')
////                ->leftJoin('number_combinations','result_details.combination_number_id','number_combinations.id')
////                ->leftJoin('single_numbers','number_combinations.single_number_id','single_numbers.id')
////                ->select('result_masters.game_date','draw_masters.end_time','number_combinations.triple_number','number_combinations.visible_triple_number','single_numbers.single_number')
////                ->get();
//            $temp_array['result'] = $data;
//            $result_array[] = $temp_array;
//
//        }
//
//        return response()->json(['success'=>1,'data'=>$result_array], 200,[],JSON_NUMERIC_CHECK);
    }





    public function get_result_sheet_by_current_date_and_game_id()
    {
        $result_dates= Carbon::today();
        $gameId= ResultMaster::distinct()->orderBy('game_id')->pluck('game_id')->take(40);
        // echo "test";
        $result_array = array();
        foreach($result_dates as $result_date){
            $temp_array['date'] = $result_date;



            $data = DrawMaster::select('result_masters.game_date','draw_masters.end_time','number_combinations.triple_number', 'result_masters.game_id',
                'number_combinations.visible_triple_number','single_numbers.single_number')
                ->leftJoin('result_masters', function ($join) use ($result_date) {
                    $join->on('draw_masters.id','=','result_masters.draw_master_id')
                        ->where('result_masters.game_date','=', $result_date);
                })
                ->leftJoin('number_combinations','result_details.combination_number_id','number_combinations.id')
                ->leftJoin('single_numbers','number_combinations.single_number_id','single_numbers.id')
                ->leftJoin('games','result_masters.game_id','games.id')
                ->where('games.id','=',$gameId)
                ->get();

            /*Do Not delete*/
            /* This is another way to use sub query */
//            $result_query =get_sql_with_bindings(ResultMaster::where('game_date',$result_date));
//            $data1 = DrawMaster::leftJoin(DB::raw("($result_query) as result_masters"),'draw_masters.id','=','result_masters.draw_master_id')
//                ->leftJoin('number_combinations','result_details.combination_number_id','number_combinations.id')
//                ->leftJoin('single_numbers','number_combinations.single_number_id','single_numbers.id')
//                ->select('result_masters.game_date','draw_masters.end_time','number_combinations.triple_number','number_combinations.visible_triple_number','single_numbers.single_number')
//                ->get();
            $temp_array['result'] = $data;
            $result_array[] = $temp_array;

        }

        return response()->json(['success'=>1,'data'=>$result_array], 200,[],JSON_NUMERIC_CHECK);
    }







    public function get_result($id)
    {
//        $result_dates= ResultMaster::distinct()->orderBy('game_date','desc')->pluck('game_date')->take(40);
//
//        $result_array = array();
//        foreach($result_dates as $result_date){
//            $temp_array['date'] = $result_date;
//
//
//
//            $data = DrawMaster::select('result_masters.game_date','draw_masters.end_time','number_combinations.triple_number', 'result_masters.game_id',
//                'number_combinations.visible_triple_number','single_numbers.single_number')
//                ->leftJoin('result_masters', function ($join) use ($id, $result_date) {
//                    $join->on('draw_masters.id','=','result_masters.draw_master_id')
//                        ->where('result_masters.game_date','=', $result_date)
//                        ->where('result_masters.game_id','=', $id);
//                })
//                ->leftJoin('number_combinations','result_details.combination_number_id','number_combinations.id')
//                ->leftJoin('single_numbers','number_combinations.single_number_id','single_numbers.id')
//               ->where('draw_masters.game_id','=', $id)
//                ->get();
//
//            /*Do Not delete*/
//            /* This is another way to use sub query */
////            $result_query =get_sql_with_bindings(ResultMaster::where('game_date',$result_date));
////            $data1 = DrawMaster::leftJoin(DB::raw("($result_query) as result_masters"),'draw_masters.id','=','result_masters.draw_master_id')
////                ->leftJoin('number_combinations','result_details.combination_number_id','number_combinations.id')
////                ->leftJoin('single_numbers','number_combinations.single_number_id','single_numbers.id')
////                ->select('result_masters.game_date','draw_masters.end_time','number_combinations.triple_number','number_combinations.visible_triple_number','single_numbers.single_number')
////                ->get();
//            $temp_array['result'] = $data;
//            $result_array[] = $temp_array;
//
//        }
//
//        return response()->json(['success'=>1,'data'=>$result_array], 200,[],JSON_NUMERIC_CHECK);
    }

    public function get_results_by_current_date($id){

//        $result_date= Carbon::today();
//
//        $result_array = array();
//        // $result_array['date'] = Carbon::today();
//
//            $data = DrawMaster::select('result_masters.game_date','draw_masters.end_time','number_combinations.triple_number', 'result_masters.game_id',
//                'number_combinations.visible_triple_number','single_numbers.single_number')
//                ->leftJoin('result_masters', function ($join) use ($id, $result_date) {
//                    $join->on('draw_masters.id','=','result_masters.draw_master_id')
//                        ->where('result_masters.game_date','=', $result_date)
//                        ->where('result_masters.game_id','=', $id);
//                })
//                ->leftJoin('number_combinations','result_details.combination_number_id','number_combinations.id')
//                ->leftJoin('single_numbers','number_combinations.single_number_id','single_numbers.id')
//               ->where('draw_masters.game_id','=', $id)
//                ->get();
//
//
//            $temp_array[] = $data;
//            $result_array['result'] = $temp_array;
//            // $result_array['result'] = $data;
//
//
//
//        return response()->json(['success'=>1,'data'=>$result_array], 200,[],JSON_NUMERIC_CHECK);




        // $result_array = array();

        // $result_array['date'] = Carbon::today();

        // $result_query =get_sql_with_bindings(ResultMaster::where('game_date', Carbon::today()));
        // $data = DrawMaster::leftJoin(DB::raw("($result_query) as result_masters"),'draw_masters.id','=','result_masters.draw_master_id')
        //     ->leftJoin('number_combinations','result_details.combination_number_id','number_combinations.id')
        //     ->leftJoin('single_numbers','number_combinations.single_number_id','single_numbers.id')
        //     ->select('result_masters.game_date','draw_masters.end_time','number_combinations.triple_number','number_combinations.visible_triple_number','single_numbers.single_number')
        //     ->get();
        // $result_array['result'] = $data;


        // return response()->json(['success'=>1,'data'=>$result_array], 200,[],JSON_NUMERIC_CHECK);

    }



    public function get_result_today_last_by_game($id){
        $today= Carbon::today()->format('Y-m-d');
        $return_array = [];
        $draw_id = [];

        $resultMastersCheck = ResultMaster::select('id')->whereGameId($id)->whereGameDate($today)->get();

        $sizeOfResultMaster = Cache::remember('sizeOfResultMasterSeven'.$id, 3000000, function () use ($resultMastersCheck) {
            return sizeof($resultMastersCheck);
        });

        if(($sizeOfResultMaster === sizeof($resultMastersCheck)) && (Cache::has('returnArraySeven'.$id) == 1)){
            $data = Cache::get('returnArraySeven'.$id);
            return response()->json(['success'=>1,'data' => $data], 200);
        }else {
            $singleNumber = DB::select("select draw_masters.id as draw_id ,draw_masters.visible_time as draw_time ,result_details.multiplexer, single_numbers.single_number from result_masters
                inner join result_details on result_details.result_master_id = result_masters.id
                inner join single_numbers on single_numbers.id = result_details.combination_number_id
                inner join draw_masters on draw_masters.id = result_masters.draw_master_id
                where result_masters.game_id = 1 and result_masters.game_date = ?
                order by draw_masters.end_time", [$today]);

            $return_array = $singleNumber;
        }

//        $resultMasters = ResultMaster::select('id','draw_master_id','game_date')->whereGameId($id)->whereGameDate($today)->orderBy('id','DESC')->limit(7)->get();




        Cache::forget('returnArraySeven'.$id);
        Cache::forget('sizeOfResultMasterSeven'.$id);
        Cache::remember('returnArraySeven'.$id, 3000000, function () use ($return_array) {
            return $return_array;
        });
        Cache::remember('sizeOfResultMasterSeven'.$id, 3000000, function () use ($resultMastersCheck) {
            return sizeof($resultMastersCheck);
        });

        return response()->json(['success'=>1, 'data' => $return_array], 200);

    }


    public function get_result_today_by_game_Asc($id){
        $today= Carbon::today()->format('Y-m-d');
        $return_array = [];
        $draw_id = [];

//        $resultMastersCheck = ResultMaster::select('id')->whereGameId($id)->whereGameDate($today)->get();

        $resultMasters = ResultMaster::select('id','draw_master_id','game_date')->whereGameId($id)->whereGameDate($today)->orderBy('id','DESC')->get();

        $sizeOfResultMaster = Cache::remember('sizeOfResultMasterAsc'.$id, 3000000, function () use ($resultMasters) {
            return sizeof($resultMasters);
        });

        if(($sizeOfResultMaster === sizeof($resultMasters)) && (Cache::has('returnArrayAsc'.$id) == 1)){
            $data = Cache::get('returnArrayAsc'.$id);
            return response()->json(['success'=>1, 'data' => $data], 200);
        }

        if($id == 1){
            foreach ($resultMasters as $resultMaster){
                $temp = [
                    'draw_id' => $resultMaster->draw_master_id,

                    'draw_time' => (DB::select("select draw_masters.visible_time from result_masters
                    inner join draw_masters ON draw_masters.id = result_masters.draw_master_id
                    where result_masters.id = ".$resultMaster->id))[0]->visible_time,

                    'multiplexer' => (DB::select("select single_numbers.single_number, result_masters.draw_master_id, result_masters.game_id, result_details.multiplexer, result_details.result_master_id from result_masters
                    inner join result_details on result_details.result_master_id = result_masters.id
                    inner join single_numbers on single_numbers.id = result_details.combination_number_id
                    where result_details.game_type_id = 1 and result_details.result_master_id = ".$resultMaster->id))[0]->multiplexer,

                    'single_number' => (DB::select("select single_numbers.single_number, result_masters.draw_master_id, result_masters.game_id, result_details.multiplexer, result_details.result_master_id from result_masters
                    inner join result_details on result_details.result_master_id = result_masters.id
                    inner join single_numbers on single_numbers.id = result_details.combination_number_id
                    where result_details.game_type_id = 1 and result_details.result_master_id = ".$resultMaster->id))[0]->single_number,

                    'double_number' => (DB::select("select double_number_combinations.visible_double_number, result_masters.draw_master_id, result_masters.game_id, result_details.multiplexer, result_details.result_master_id from result_masters
                    inner join result_details on result_details.result_master_id = result_masters.id
                    inner join double_number_combinations on double_number_combinations.id = result_details.combination_number_id
                    where result_details.game_type_id = 5 and result_details.result_master_id = ".$resultMaster->id))[0]->visible_double_number,

                    'triple_number' => (DB::select("select number_combinations.visible_triple_number, result_masters.draw_master_id, result_masters.game_id, result_details.multiplexer, result_details.result_master_id from result_masters
                    inner join result_details on result_details.result_master_id = result_masters.id
                    inner join number_combinations on number_combinations.id = result_details.combination_number_id
                    where result_details.game_type_id = 2 and result_details.result_master_id = ".$resultMaster->id))[0]->visible_triple_number
                ];
                array_push($return_array,$temp);
                array_push($draw_id,$resultMaster->draw_master_id);
            }

        }else if($id == 2){
            $twelveCard = DB::select("select draw_masters.id as draw_id ,draw_masters.visible_time as draw_time ,result_details.multiplexer, card_combinations.rank_name, card_combinations.suit_name, card_combinations.rank_initial from result_masters
                inner join result_details on result_details.result_master_id = result_masters.id
                inner join card_combinations on card_combinations.id = result_details.combination_number_id
                inner join draw_masters on draw_masters.id = result_masters.draw_master_id
                where result_masters.game_id = 2 and result_masters.game_date = ? order by result_masters.id desc limit 7",[$today]);
            $return_array = $twelveCard;

            foreach ($twelveCard as $x){
                array_push($draw_id,$x->draw_id);
            }


        }else if($id == 3){
            $sixteenCard = DB::select("select draw_masters.id as draw_id ,draw_masters.visible_time as draw_time ,result_details.multiplexer, card_combinations.rank_name, card_combinations.suit_name, card_combinations.rank_initial from result_masters
                inner join result_details on result_details.result_master_id = result_masters.id
                inner join card_combinations on card_combinations.id = result_details.combination_number_id
                inner join draw_masters on draw_masters.id = result_masters.draw_master_id
                where result_masters.game_id = 3 and result_masters.game_date = ? order by result_masters.id desc limit 7",[$today]);
            $return_array = $sixteenCard;

            foreach ($sixteenCard as $x){
                array_push($draw_id,$x->draw_id);
            }

        }else if($id == 4){
            $singleNumber = DB::select("select draw_masters.id as draw_id ,draw_masters.visible_time as draw_time ,result_details.multiplexer, single_numbers.single_number from result_masters
                inner join result_details on result_details.result_master_id = result_masters.id
                inner join single_numbers on single_numbers.id = result_details.combination_number_id
                inner join draw_masters on draw_masters.id = result_masters.draw_master_id
                where result_masters.game_id = 4 and result_masters.game_date = ? order by result_masters.id desc limit 7",[$today]);

            $return_array = $singleNumber;

            foreach ($singleNumber as $x){
                array_push($draw_id,$x->draw_id);
            }


        }else if($id == 5){
            foreach ($resultMasters as $resultMaster){
                $temp = [
                    'draw_id' => $resultMaster->draw_master_id,

                    'draw_time' => (DB::select("select draw_masters.visible_time from result_masters
                    inner join draw_masters ON draw_masters.id = result_masters.draw_master_id
                    where result_masters.id = ".$resultMaster->id))[0]->visible_time,

                    'multiplexer' => (DB::select("select double_number_combinations.visible_double_number, result_masters.draw_master_id, result_masters.game_id, result_details.multiplexer, result_details.result_master_id from result_masters
                    inner join result_details on result_details.result_master_id = result_masters.id
                    inner join double_number_combinations on double_number_combinations.id = result_details.combination_number_id
                    where result_details.game_type_id = 7 and result_details.result_master_id =".$resultMaster->id))[0]->multiplexer,

                    'double_number' => (DB::select("select double_number_combinations.visible_double_number, result_masters.draw_master_id, result_masters.game_id, result_details.multiplexer, result_details.result_master_id from result_masters
                    inner join result_details on result_details.result_master_id = result_masters.id
                    inner join double_number_combinations on double_number_combinations.id = result_details.combination_number_id
                    where result_details.game_type_id = 7 and result_details.result_master_id = ".$resultMaster->id))[0]->visible_double_number,

                    'andar_number' => (DB::select("select andar_numbers.andar_number ,result_masters.draw_master_id, result_masters.game_id, result_details.multiplexer, result_details.result_master_id  from result_masters
                    inner join result_details on result_details.result_master_id = result_masters.id
                    inner join andar_numbers on andar_numbers.id = result_details.combination_number_id
                    where result_details.game_type_id = 8 and result_details.result_master_id = ".$resultMaster->id))[0]->andar_number,

                    'bahar_number' => (DB::select("select bahar_numbers.bahar_number ,result_masters.draw_master_id, result_masters.game_id, result_details.multiplexer, result_details.result_master_id  from result_masters
                    inner join result_details on result_details.result_master_id = result_masters.id
                    inner join bahar_numbers on bahar_numbers.id = result_details.combination_number_id
                    where result_details.game_type_id = 9 and result_details.result_master_id = ".$resultMaster->id))[0]->bahar_number
                ];
                array_push($return_array,$temp);
                array_push($draw_id,$resultMaster->draw_master_id);
            }
        }else if($id == 6){
            $rolletNumber = DB::select("select draw_masters.id as draw_id ,draw_masters.visible_time as draw_time ,result_details.multiplexer, rollet_numbers.rollet_number from result_masters
                inner join result_details on result_details.result_master_id = result_masters.id
                inner join rollet_numbers on rollet_numbers.id = result_details.combination_number_id
                inner join draw_masters on draw_masters.id = result_masters.draw_master_id
                where result_masters.game_id = 6 and result_masters.game_date = ?
                order by draw_masters.end_time",[$today]);

            $return_array = $rolletNumber;

            foreach ($rolletNumber as $x){
                array_push($draw_id,$x->draw_id);
            }
        }


        else{
            return response()->json(['success'=> 0, 'message' => 'Invalid game id'], 200);
        }

        Cache::forget('returnArrayAsc'.$id);
        Cache::forget('sizeOfResultMasterAsc'.$id);
        Cache::remember('returnArrayAsc'.$id, 3000000, function () use ($return_array) {
            return $return_array;
        });
        Cache::remember('sizeOfResultMasterAsc'.$id, 3000000, function () use ($resultMasters) {
            return sizeof($resultMasters);
        });

//        $return_array = collect($return_array)->sortBy('draw_time')->reverse()->toArray();
//        $return_array = collect($return_array)->sortBy('draw_time')->toArray();

        return response()->json(['success'=>1, 'data' => $return_array], 200);
    }

    public function dateCheck($today, $sent){
        if($today == $sent){
            return true;
        }else{
            return false;
        }
    }

    public function get_result_today_by_game(Request $request){

        $tempToday = Carbon::today()->format("Y-m-d");
        $req = Carbon::createFromFormat('Y-m-d', ((object)$request->json()->all())->date)->format("Y-m-d");

        if($this->dateCheck($tempToday,$req) == false){

            $singleNumber = Cache::remember($req, 3000000, function () use ($req) {
                return DB::select("select draw_masters.id as draw_id ,draw_masters.visible_time as draw_time ,result_details.multiplexer, single_numbers.single_number from result_masters
                inner join result_details on result_details.result_master_id = result_masters.id
                inner join single_numbers on single_numbers.id = result_details.combination_number_id
                inner join draw_masters on draw_masters.id = result_masters.draw_master_id
                where result_masters.game_id = 1 and result_masters.game_date = ?
                order by draw_masters.end_time", [$req]);
            });

            return response()->json(['success'=>1,'data' => $singleNumber], 200);
        }

        $id = 1;
        $today= Carbon::today()->format('Y-m-d');
        $return_array = [];
        $draw_id = [];
//        $resultMasters = ResultMaster::select('id','draw_master_id','game_date')->whereGameId($id)->whereGameDate($today)->orderBy('end_time')->get();
        $resultMasters = DB::select("select result_masters.id, result_masters.draw_master_id, result_masters.game_date from result_masters
            inner join draw_masters on result_masters.draw_master_id = draw_masters.id
            where result_masters.game_id = 1 and result_masters.game_date = ?
            order by draw_masters.end_time", [$today]);

        $sizeOfResultMaster = Cache::remember('sizeOfResultMaster'.$id, 3000000, function () use ($resultMasters) {
            return sizeof($resultMasters);
        });

        if(($sizeOfResultMaster === sizeof($resultMasters)) && (Cache::has('returnArray'.$id) == 1)){
            $data = Cache::get('returnArray'.$id);
            return response()->json(['success'=>1,'data' => $data], 200);
        }
        else {
            $singleNumber = DB::select("select draw_masters.id as draw_id ,draw_masters.visible_time as draw_time ,result_details.multiplexer, single_numbers.single_number from result_masters
                inner join result_details on result_details.result_master_id = result_masters.id
                inner join single_numbers on single_numbers.id = result_details.combination_number_id
                inner join draw_masters on draw_masters.id = result_masters.draw_master_id
                where result_masters.game_id = 1 and result_masters.game_date = ?
                order by draw_masters.end_time", [$today]);

            $return_array = $singleNumber;
        }

        Cache::forget('returnArray'.$id);
        Cache::forget('sizeOfResultMaster'.$id);
        Cache::remember('returnArray'.$id, 3000000, function () use ($return_array) {
            return $return_array;
        });
        Cache::remember('sizeOfResultMaster'.$id, 3000000, function () use ($resultMasters) {
            return sizeof($resultMasters);
        });

        return response()->json(['success'=>1, 'data' => $return_array], 200);

    }

    public function save_auto_result($draw_id, $game_type_id, $combination_number_id, $multiplexer)
    {


        $today= Carbon::today()->format('Y-m-d');
        $game_id = (DrawMaster::whereId($draw_id)->first())->game_id;
        $game_gen = (Game::whereId($game_id)->first())->auto_generate;
//        $game_multiplexer = (GameType::find($game_type_id))->multiplexer;
        $game_multiplexer = $multiplexer;

        if($game_gen == "no"){
            return response()->json(['success'=>1, 'data' => 'Auto generate is deactivated'], 200);
        }

        $ManualGameCheck = ManualResult::whereGameDate($today)->whereGameTypeId($game_type_id)->first();
        if($ManualGameCheck){
            $combination_number_id = $ManualGameCheck->combination_number_id;
            $game_multiplexer = $ManualGameCheck->multiplexer;
        }

        $set_game_date = Carbon::today()->format('Y-m-d');

//        $resultMaster = new ResultMaster();
//        $resultMaster->draw_master_id = $draw_id;
//        $resultMaster->game_id = $game_id;
//        $resultMaster->game_date = Carbon::today();
//        $resultMaster-> save();
//
//        return response()->json(['success'=>1, 'data' => $resultMaster], 200);

        $resultMaster = ResultMaster::whereGameId($game_id)->whereDrawMasterId($draw_id)->whereGameDate($today)->first();
        $next_day = Carbon::today()->addDays(1)->format('Y-m-d');

        if($resultMaster){
//            $set_game_date = Carbon::today()->addDays(1)->format('Y-m-d');
            if($game_id == 1 || $game_id == 5){
                if($game_id == 1) {
                    $count = DB::select("select COUNT(id) as total_count from result_details where result_master_id = ".$resultMaster->id)[0]->total_count;
                    if($count>=3){

                        $resultMaster = new ResultMaster();
                        $resultMaster->draw_master_id = $draw_id;
                        $resultMaster->game_id = $game_id;
                        $resultMaster->game_date = $next_day;
                        $resultMaster-> save();

                        $resultDetail = new ResultDetail();
                        $resultDetail->result_master_id = $resultMaster->id;
                        $resultDetail->game_type_id = $game_type_id;
                        $resultDetail->combination_number_id = $combination_number_id;
                        $resultDetail->multiplexer = $game_multiplexer;
                        $resultDetail->save();
                }else{
                        $resultDetail = new ResultDetail();
                        $resultDetail->result_master_id = $resultMaster->id;
                        $resultDetail->game_type_id = $game_type_id;
                        $resultDetail->combination_number_id = $combination_number_id;
                        $resultDetail->multiplexer = $game_multiplexer;
                        $resultDetail->save();
                    }
                }else{
                    $resultDetail = new ResultDetail();
                    $resultDetail->result_master_id = $resultMaster->id;
                    $resultDetail->game_type_id = $game_type_id;
                    $resultDetail->combination_number_id = $combination_number_id;
                    $resultDetail->multiplexer = $game_multiplexer;
                    $resultDetail->save();
                }

            }else if($game_id == 2 || $game_id == 3 || $game_id == 4){
                $resultMaster = new ResultMaster();
                $resultMaster->draw_master_id = $draw_id;
                $resultMaster->game_id = $game_id;
                $resultMaster->game_date = Carbon::today()->addDays(1)->format('Y-m-d');
                $resultMaster-> save();

                $resultDetail = new ResultDetail();
                $resultDetail->result_master_id = $resultMaster->id;
                $resultDetail->game_type_id = $game_type_id;
                $resultDetail->combination_number_id = $combination_number_id;
                $resultDetail->multiplexer = $game_multiplexer;
                $resultDetail->save();
            }
        }else{
            $resultMaster = new ResultMaster();
            $resultMaster->draw_master_id = $draw_id;
            $resultMaster->game_id = $game_id;
            $resultMaster->game_date = $set_game_date;
            $resultMaster-> save();

            $resultDetail = new ResultDetail();
            $resultDetail->result_master_id = $resultMaster->id;
            $resultDetail->game_type_id = $game_type_id;
            $resultDetail->combination_number_id = $combination_number_id;
            $resultDetail->multiplexer = $game_multiplexer;
            $resultDetail->save();
        }


        if(isset($resultMaster->id)){
            return response()->json(['success'=>1, 'data' => 'added result'], 200);
        }else{
            return response()->json(['success'=>0, 'data' => 'result not added'], 500);
        }
    }


    public function save_auto_result_previous($draw_id)
    {

        $game_id = (DrawMaster::whereId($draw_id)->first())->game_id;
        $game_gen = (Game::whereId($game_id)->first())->auto_generate;

                $manualResult = ManualResult::where('game_date',Carbon::today())
                    ->where('draw_master_id',$draw_id)
                    ->where('game_id',$game_id)
                    ->first();
                if(!empty($manualResult)){
                    $number_combination_for_result = $manualResult->number_combination_id;
                    $gameId = $manualResult->game_id;
                }else if ($game_gen == 'yes'){
                    $selectRandomResult = NumberCombination::all()->random(1)->first();
                    $number_combination_for_result = $selectRandomResult->id;
                    $gameId = $game_id;
                }else{
                    return response()->json(['success'=>1, 'data' => 'added result'], 200);
                }
                $resultMaster = new ResultMaster();
                $resultMaster->draw_master_id = $draw_id;
                $resultMaster->number_combination_id = $number_combination_for_result;
                $resultMaster->game_id = $gameId;
                $resultMaster->game_date = Carbon::today();
                $resultMaster->save();

        if(isset($resultMaster->id)){
            return response()->json(['success'=>1, 'data' => 'added result'], 200);
        }else{
            return response()->json(['success'=>0, 'data' => 'result not added'], 500);
        }
    }


    public function get_last_result(){
//        $result_date= Carbon::today();
//
//        $result_query =get_sql_with_bindings(ResultMaster::where('game_date', Carbon::today()));
//        $data = DrawMaster::leftJoin(DB::raw("($result_query) as result_masters"),'draw_masters.id','=','result_masters.draw_master_id')
//            ->leftJoin('number_combinations','result_details.combination_number_id','number_combinations.id')
//            ->leftJoin('single_numbers','number_combinations.single_number_id','single_numbers.id')
//            ->select('result_masters.game_date','draw_masters.end_time','number_combinations.triple_number','number_combinations.visible_triple_number','single_numbers.single_number')
//            ->orderBy('result_masters.draw_master_id','desc')
//            ->whereNotNull('single_numbers.single_number')
//            ->where(DB::raw('date(result_masters.created_at)','2022-01-04'))
//            ->first();



        $today= Carbon::today()->format('Y-m-d');
        $id = 1;

        $return_array = [];
        $draw_id = [];
        $resultMasters = ResultMaster::whereGameId($id)->whereGameDate($today)->get();

        if($id == 1){
            foreach ($resultMasters as $resultMaster){
                $temp = [
                    'draw_id' => $resultMaster->draw_master_id,

                    'draw_time' => (DB::select("select draw_masters.visible_time from result_masters
                    inner join draw_masters ON draw_masters.id = result_masters.draw_master_id
                    where result_masters.id = ".$resultMaster->id))[0]->visible_time,

                    'multiplexer' => (DB::select("select single_numbers.single_number, result_masters.draw_master_id, result_masters.game_id, result_details.multiplexer, result_details.result_master_id from result_masters
                    inner join result_details on result_details.result_master_id = result_masters.id
                    inner join single_numbers on single_numbers.id = result_details.combination_number_id
                    where result_details.game_type_id = 1 and result_details.result_master_id = ".$resultMaster->id))[0]->multiplexer,

                    'single_number' => (DB::select("select single_numbers.single_number, result_masters.draw_master_id, result_masters.game_id, result_details.multiplexer, result_details.result_master_id from result_masters
                    inner join result_details on result_details.result_master_id = result_masters.id
                    inner join single_numbers on single_numbers.id = result_details.combination_number_id
                    where result_details.game_type_id = 1 and result_details.result_master_id = ".$resultMaster->id))[0]->single_number,

                    'double_number' => (DB::select("select double_number_combinations.visible_double_number, result_masters.draw_master_id, result_masters.game_id, result_details.multiplexer, result_details.result_master_id from result_masters
                    inner join result_details on result_details.result_master_id = result_masters.id
                    inner join double_number_combinations on double_number_combinations.id = result_details.combination_number_id
                    where result_details.game_type_id = 5 and result_details.result_master_id = ".$resultMaster->id))[0]->visible_double_number,

                    'triple_number' => (DB::select("select number_combinations.visible_triple_number, result_masters.draw_master_id, result_masters.game_id, result_details.multiplexer, result_details.result_master_id from result_masters
                    inner join result_details on result_details.result_master_id = result_masters.id
                    inner join number_combinations on number_combinations.id = result_details.combination_number_id
                    where result_details.game_type_id = 2 and result_details.result_master_id = ".$resultMaster->id))[0]->visible_triple_number
                ];
                array_push($return_array,$temp);
                array_push($draw_id,$resultMaster->draw_master_id);
            }


        }else if($id == 2){
            $twelveCard = DB::select("select draw_masters.id as draw_id ,draw_masters.visible_time as draw_time ,result_details.multiplexer, card_combinations.rank_name, card_combinations.suit_name, card_combinations.rank_initial from result_masters
                inner join result_details on result_details.result_master_id = result_masters.id
                inner join card_combinations on card_combinations.id = result_details.combination_number_id
                inner join draw_masters on draw_masters.id = result_masters.draw_master_id
                where result_masters.game_id = 2 and result_masters.game_date = ?",[$today]);
            $return_array = $twelveCard;

            foreach ($twelveCard as $x){
                array_push($draw_id,$x->draw_id);
            }

        }else if($id == 3){
            $sixteenCard = DB::select("select draw_masters.id as draw_id ,draw_masters.visible_time as draw_time ,result_details.multiplexer, card_combinations.rank_name, card_combinations.suit_name, card_combinations.rank_initial from result_masters
                inner join result_details on result_details.result_master_id = result_masters.id
                inner join card_combinations on card_combinations.id = result_details.combination_number_id
                inner join draw_masters on draw_masters.id = result_masters.draw_master_id
                where result_masters.game_id = 3 and result_masters.game_date = ?",[$today]);
            $return_array = $sixteenCard;

            foreach ($sixteenCard as $x){
                array_push($draw_id,$x->draw_id);
            }

        }else if($id == 4){
            $singleNumber = DB::select("select draw_masters.id as draw_id ,draw_masters.visible_time as draw_time ,result_details.multiplexer, single_numbers.single_number from result_masters
                inner join result_details on result_details.result_master_id = result_masters.id
                inner join single_numbers on single_numbers.id = result_details.combination_number_id
                inner join draw_masters on draw_masters.id = result_masters.draw_master_id
                where result_masters.game_id = 4 and result_masters.game_date = ?",[$today]);

            $return_array = $singleNumber;

            foreach ($singleNumber as $x){
                array_push($draw_id,$x->draw_id);
            }

        }else if($id == 5){
            foreach ($resultMasters as $resultMaster){
                $temp = [
                    'draw_id' => $resultMaster->draw_master_id,

                    'draw_time' => (DB::select("select draw_masters.visible_time from result_masters
                    inner join draw_masters ON draw_masters.id = result_masters.draw_master_id
                    where result_masters.id = ".$resultMaster->id))[0]->visible_time,

                    'multiplexer' => (DB::select("select single_numbers.single_number, result_masters.draw_master_id, result_masters.game_id, result_details.multiplexer, result_details.result_master_id from result_masters
                    inner join result_details on result_details.result_master_id = result_masters.id
                    inner join single_numbers on single_numbers.id = result_details.combination_number_id
                    where result_details.game_type_id = 7 and result_details.result_master_id = ".$resultMaster->id))[0]->multiplexer,

                    'double_number' => (DB::select("select double_number_combinations.visible_double_number, result_masters.draw_master_id, result_masters.game_id, result_details.multiplexer, result_details.result_master_id from result_masters
                    inner join result_details on result_details.result_master_id = result_masters.id
                    inner join double_number_combinations on double_number_combinations.id = result_details.combination_number_id
                    where result_details.game_type_id = 7 and result_details.result_master_id = ".$resultMaster->id))[0]->visible_double_number,

                    'andar_number' => (DB::select("select andar_numbers.andar_number ,result_masters.draw_master_id, result_masters.game_id, result_details.multiplexer, result_details.result_master_id  from result_masters
                    inner join result_details on result_details.result_master_id = result_masters.id
                    inner join andar_numbers on andar_numbers.id = result_details.combination_number_id
                    where result_details.game_type_id = 8 and result_details.result_master_id = ".$resultMaster->id))[0]->andar_number,

                    'bahar_number' => (DB::select("select bahar_numbers.bahar_number ,result_masters.draw_master_id, result_masters.game_id, result_details.multiplexer, result_details.result_master_id  from result_masters
                    inner join result_details on result_details.result_master_id = result_masters.id
                    inner join bahar_numbers on bahar_numbers.id = result_details.combination_number_id
                    where result_details.game_type_id = 9 and result_details.result_master_id = ".$resultMaster->id))[0]->bahar_number
                ];
                array_push($return_array,$temp);
                array_push($draw_id,$resultMaster->draw_master_id);
            }
        }

        else{
            return response()->json(['success'=> 0, 'message' => 'Invalid game id'], 200);
        }

        return response()->json(['success'=>1, 'data' => $return_array], 200);


//        return response()->json(['success'=> 2, 'data' => $data], 200);
    }

    public function get_result_by_date(Request $request){

//        $date= $request['date'];
        // return response()->json(['success'=>1,'data'=>$date], 200,[],JSON_NUMERIC_CHECK);

        $result_array['date'] = $request['date'];

        $result_query =get_sql_with_bindings(ResultMaster::where('game_date', $request['date']));
        $data = DrawMaster::leftJoin(DB::raw("($result_query) as result_masters"),'draw_masters.id','=','result_masters.draw_master_id')
            ->leftJoin('number_combinations','result_details.combination_number_id','number_combinations.id')
            ->leftJoin('single_numbers','number_combinations.single_number_id','single_numbers.id')
            ->select('result_masters.game_date','draw_masters.end_time','number_combinations.triple_number','number_combinations.visible_triple_number','single_numbers.single_number')
            ->get();
        $result_array['result'] = $data;



        return response()->json(['success'=>1,'data'=>$data], 200,[],JSON_NUMERIC_CHECK);

    }


}
