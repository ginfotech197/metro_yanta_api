<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DoubleNumberCombination;
use App\Models\Game;
use App\Models\GameType;
use App\Models\ManualResult;
use App\Models\NumberCombination;
use App\Models\PlayDetails;
use App\Models\PlayMaster;
use App\Models\ResultDetail;
use App\Models\ResultMaster;
use App\Models\SingleNumber;
use Carbon\Carbon;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use App\Models\NextGameDraw;
use App\Models\DrawMaster;
use App\Http\Controllers\ManualResultController;
use App\Http\Controllers\NumberCombinationController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Scalar\String_;
//use App\Models\User;

class CentralController extends Controller
{

    public function createAutoResult($id){
        return $this->createResult($id, 1);
    }

    public function createResult($id, $count){

//        $gameController = new GameController();
//        $gameTotalReport = ($gameController->get_game_total_sale_today());
//        $gameTotalReport = json_decode(($gameController->get_game_total_sale_today_result_generation(1))->content(),true);
//
//        return response()->json(['success'=>2, 'message' => $gameTotalReport], 200);

        $game = Game::find($id);
        if(!$game){
            return response()->json(['success'=>0, 'message' => 'Incorrect Game Id'], 200);
        }
        if($game->active == "no"){
            return response()->json(['success'=>0, 'message' => 'Game not active'], 200);
        }

        $nextGameDrawObj = DB::select("select id,next_draw_id,last_draw_id from next_game_draws where game_id = ?",[$id])[0];

//        $single_numbers = Cache::remember('get_all_single_number', 3000000, function () {
//            return SingleNumber::select('id','single_number')->get();
//        });
//
//        $double_numbers = Cache::remember('get_all_double_number', 3000000, function () {
//            return DB::select("select id, single_number_id, double_number, visible_double_number, andar_number_id, bahar_number_id from double_number_combinations");
//        });

        $today= Carbon::today()->format('Y-m-d');
        $playMasterControllerObj = new PlayMasterController();
        $resultMasterControllerObj = new ResultMasterController();

        $checkMultiplexerStatus = (Game::select('multiplexer_random')->whereId($id)->first())->multiplexer_random;

        $game_multiplexer = $checkMultiplexerStatus == 'yes'? 1 : (GameType::whereGameId($id)->first())->multiplexer;

        $gameTemp_multiplexer = (GameType::whereGameId($id)->first())->multiplexer;

        $null_multiplexer = [2,4,3];

        $checkCount = (GameType::whereGameId($id)->first())->counter;



//            $nextGameDrawObj = NextGameDraw::whereGameId($id)->first();
        $nextDrawId = $nextGameDrawObj->next_draw_id;
        $lastDrawId = $nextGameDrawObj->last_draw_id;

        $singleNumber = (GameType::find(1));

        $totalSale = $playMasterControllerObj->get_total_sale($today,$lastDrawId,1);
        $payout = (($totalSale * ($singleNumber->payout)) / 100)/$game_multiplexer;
        $singleValue = floor($payout / $singleNumber->winning_price);

//            $singleValue = (($playMasterControllerObj->get_total_sale($today,$lastDrawId,6) * (($singleNumber->payout)/100))/$game_multiplexer)/($singleNumber->winning_price);

//            $singleNumberTargetData = DB::select("select * from play_details
//                inner join play_masters on play_details.play_master_id = play_masters.id
//                where quantity <= ? and game_type_id = 6 and date(play_details.created_at) = ? and play_masters.draw_master_id = ?
//                order by quantity desc
//                limit 1",[$singleValue, $today, $lastDrawId]);

        $singleNumberTargetData = DB::select("select sum(quantity) as quantity,combination_number_id from play_details
                inner join play_masters on play_details.play_master_id = play_masters.id
                where play_masters.draw_master_id = ? and play_masters.game_id = 1 and play_details.game_type_id = 1 and date(play_masters.created_at) = ?
                GROUP by combination_number_id
                having sum(play_details.quantity)<= ?
                order by rand()
                limit 1",[$lastDrawId,$today,$singleValue]);

        //empty check
        if(empty($singleNumberTargetData)) {
            $singleNumberTargetData = DB::select("select id as combination_number_id, 0 as quantity from single_numbers
                    where id not in (select combination_number_id from play_details
                    inner join play_masters on play_details.play_master_id = play_masters.id
                    where game_type_id = 1 and date(play_details.created_at) = ? and play_masters.draw_master_id = ?)
                    order by RAND()
                    limit 1",[$today, $lastDrawId]);
        }

        // greater target value
        if(empty($singleNumberTargetData)){
            $singleNumberTargetData = DB::select("select * from (select sum(quantity) as quantity,combination_number_id from play_details
                inner join play_masters on play_details.play_master_id = play_masters.id
                 where play_masters.draw_master_id = ? and play_masters.game_id = 1 and play_details.game_type_id = 1 and date(play_masters.created_at) = ?
                GROUP by combination_number_id
                having sum(play_details.quantity)>= ?) as table1
                order by quantity
                LIMIT 1",[$lastDrawId,$today,$singleValue]);
        }

//            if(($singleNumberTargetData[0]->quantity) > $singleValue){
//                $game_multiplexer = 1;
//            }elseif ($singleNumberTargetData[0]->quantity == 0){
//                $randNum = rand(0, 10);
//                $game_multiplexer = $randNum>7 ? $null_multiplexer[array_rand($null_multiplexer,1)] : 1;
//            }

        if($checkMultiplexerStatus == 'yes') {
            if (($singleNumberTargetData[0]->quantity) > $singleValue) {
                $game_multiplexer = 1;
            } elseif ($singleNumberTargetData[0]->quantity == 0) {
                $randNum = rand(0, 10);
                $game_multiplexer = $randNum > 7 ? $null_multiplexer[array_rand($null_multiplexer, 1)] : 1;
            } elseif (($singleNumberTargetData[0]->quantity) < $singleValue) {
                $checkMultiplexer = $singleNumberTargetData[0]->quantity * $singleNumber->winning_price * 2;
                if ($checkMultiplexer < $payout) {
                    $game_multiplexer = rand(1, 2);
                }
            }
        }

//            if($checkCount == 0) {
//                $this->checkAndSetDefaultPayout($id);
//            }else{
//                DB::select("update game_types set counter = (counter - 1) where game_id = ".$id);
//            }

        $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,1,$singleNumberTargetData[0]->combination_number_id,$game_multiplexer))->content(),true);

//            if($playMasterSaveCheck['success'] == 0){
//                return response()->json(['success'=>0, 'message' => 'Save error single number'], 401);
//            }





//        return $null_multiplexer[array_rand($null_multiplexer,1)];

//        if($game_multiplexer == 1){
////            $tempM= [1,2,3];
//            $tempM= [1];
//            $game_multiplexer = $tempM[array_rand($tempM)];
//        }

//        enable when concept of multiplexer comes
//        $ManualGameCheck = ManualResult::whereGameDate($today)->whereGameTypeId((GameType::whereGameId($id)->first())->id)->first();
//        if($ManualGameCheck){
//            $game_multiplexer = $ManualGameCheck->multiplexer;
//        }

//        if($id == 1){
//
////            $nextGameDrawObj = NextGameDraw::whereGameId($id)->first();
//            $nextDrawId = $nextGameDrawObj->next_draw_id;
//            $lastDrawId = $nextGameDrawObj->last_draw_id;
//
//            //payouts
////            $singleNumber = (GameType::find(1));
////            $doubleNumber = (GameType::find(5));
////            $tripleNumber = (GameType::find(2));
//
//            $singleNumber = DB::select("select id,mrp,winning_price,payout,multiplexer from game_types where id = 1")[0];
//            $doubleNumber = DB::select("select id,mrp,winning_price,payout,multiplexer from game_types where id = 5")[0];
//            $tripleNumber = DB::select("select id,mrp,winning_price,payout,multiplexer from game_types where id = 2")[0];
//
//            //total sales
//            $singleNumberTotalSale = $playMasterControllerObj->get_total_sale($today,$lastDrawId,1);
//            $doubleNumberTotalSale = $playMasterControllerObj->get_total_sale($today,$lastDrawId,5);
//            $tripleNumberTotalSale = $playMasterControllerObj->get_total_sale($today,$lastDrawId,2);
//
//            $allGameTotalSale = (((($singleNumberTotalSale*($singleNumber->payout))/100)) + (($doubleNumberTotalSale*($doubleNumber->payout))/100) + (($tripleNumberTotalSale*($tripleNumber->payout))/100))/($game_multiplexer);
//
//            //triple number
//            $tripleValue = (int)($allGameTotalSale/($tripleNumber->winning_price));
//            $loopOn = 1;
//
//            $resultToBeSaved = [];
//            $gen = 0;
//
//            while(true){
//
//                $tripleNumberTargetData = DB::select("select number_combinations.visible_triple_number,play_details.quantity,play_details.combination_number_id from play_details
//                inner join number_combinations on number_combinations.id = play_details.combination_number_id
//                inner join play_masters on play_details.play_master_id = play_masters.id
//                where quantity <= ? and game_type_id = 2 and date(play_details.created_at) = ? and play_masters.draw_master_id = ?
//                order by RAND(), quantity desc
//                ",[$tripleValue, $today, $lastDrawId]);
//
//
//                if(!empty($tripleNumberTargetData)){
//                    foreach ($tripleNumberTargetData as $tripleData){
//                        $splitNumber = str_split($tripleData->visible_triple_number);
////                        $singleNumberValue = (SingleNumber::select()->whereSingleNumber($splitNumber[2])->first())->id;
//                        $singleNumberValue = (collect($single_numbers)->where('single_number', $splitNumber[2])->first())->id;
////                        $doubleNumberValue = (DoubleNumberCombination::select()->whereDoubleNumber($splitNumber[1].$splitNumber[2])->first())->id;
//                        $doubleNumberValue = (collect($double_numbers)->where('double_number', $splitNumber[1].$splitNumber[2])->first())->id;
//                        $doubleNumberQuantity = 0;
//                        $singleNumberQuantity = 0;
//
//                        $doubleNumberTargetData = DB::select("select ifnull(sum(quantity),0) as quantity from play_details
//                        inner join play_masters on play_details.play_master_id = play_masters.id
//                        where play_details.combination_number_id = ? and game_type_id = 5 and date(play_details.created_at) = ? and play_masters.draw_master_id = ?
//                        ",[$doubleNumberValue, $today, $lastDrawId]);
//
//                        if(empty($doubleNumberTargetData)) {
//                            $doubleNumberQuantity = 0;
//                        }else{
//                            $doubleNumberQuantity = $doubleNumberTargetData[0]->quantity;
//                        }
//
//                        $singleNumberTargetData = DB::select("select ifnull(sum(quantity),0) as quantity from play_details
//                        inner join play_masters on play_details.play_master_id = play_masters.id
//                        where play_details.combination_number_id = ?  and game_type_id = 1 and date(play_details.created_at) = ? and play_masters.draw_master_id = ?
//                       ",[$singleNumberValue, $today, $lastDrawId]);
//
//                        if(empty($singleNumberTargetData)) {
//                            $singleNumberQuantity = 0;
//                        }else{
//                            $singleNumberQuantity = $singleNumberTargetData[0]->quantity;
//                        }
//
//                        $totalSale = (($tripleData->quantity) * $tripleNumber->winning_price) + ($doubleNumberQuantity * $doubleNumber->winning_price) + ($singleNumberQuantity * $singleNumber->winning_price);
//
//                        if($totalSale <= $allGameTotalSale){
//                            $loopOn = 0;
//                            $gen = 1;
////                            $temp = [
////                                'single x' => $splitNumber[2],
////                                'single' => $singleNumberValue,
////                                'single quantity' => $singleNumberQuantity,
////                                'double' => $doubleNumberValue,
////                                'double quantity' => $doubleNumberQuantity,
////                                'triple' => $tripleData->combination_number_id,
//////                            'tripleNumberData' => $tripleNumberTargetData,
////                                'total_sale' => $totalSale,
////                                'allGameTotalSale' => $allGameTotalSale,
////                                '$tripleData' => $tripleData,
////                                '$splitNumber' => $splitNumber,
////                            ];
//
////                        return $temp;
//
//                            $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,1,$singleNumberValue,$game_multiplexer))->content(),true);
//                            $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,2,$tripleData->combination_number_id,$game_multiplexer))->content(),true);
//                            $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,5,$doubleNumberValue,$game_multiplexer))->content(),true);
//
////                            array_push($resultToBeSaved, $temp);
//                            break;
//                        }
//                    }
//                }else{
//                    break;
//                }
//
//
//                if($loopOn == 1){
//                    if($tripleValue > 0 ){
//                        $tripleValue = $tripleValue - 1;
//                        continue;
//                    }else{
//                        break;
//                    }
//                }else{
//                    break;
//                }
//            }
//
//            if($gen == 0){
//
//                if(empty($tripleNumberTargetData)){
//
//                    $tripleNumberTargetData = $this->checkSmallerTotalSale($lastDrawId);
//
//                    $splitNumber = str_split($tripleNumberTargetData[0]->visible_triple_number);
//                    $singleNumberValue = (collect($single_numbers)->where('single_number', $splitNumber[2])->first())->id;
//                    $doubleNumberValue = (collect($double_numbers)->where('double_number', $splitNumber[1].$splitNumber[2])->first())->id;
//
//                    $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,1,$singleNumberValue,$game_multiplexer))->content(),true);
//                    $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,2,$tripleNumberTargetData[0]->combination_number_id,$game_multiplexer))->content(),true);
//                    $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,5,$doubleNumberValue,$game_multiplexer))->content(),true);
//                }
//            }
//
////            return response()->json(['single_number'=>$singleNumberTotalSale
////                , 'double_number' => $doubleNumberTotalSale
////                , 'triple_number' => $tripleNumberTotalSale
////                , 'totalSale' => $allGameTotalSale
////                , 'tripleValue' => $tripleValue
////                , 'tripleAmount' => $tripleNumberAmount
////                , 'tripleTargetData' => $tripleNumberTargetData
////                , 'doubleValue' => $doubleValue
////                , 'doubleAmount' => $doubleNumberAmount
////                , 'singleValue' => $singleValue
////                , 'singleAmount' => $singleNumberAmount
////                , 'returnCheck' => $playMasterSaveCheck['success']
////            ], 200);
//
////            return $temp;
//        }

//        if($id == 2){
//
////            $nextGameDrawObj = NextGameDraw::whereGameId($id)->first();
//            $nextDrawId = $nextGameDrawObj->next_draw_id;
//            $lastDrawId = $nextGameDrawObj->last_draw_id;
//
//            $totalSale = $playMasterControllerObj->get_total_sale($today,$lastDrawId,3);
//            $gameType = GameType::find(3);
//            $payout = (($totalSale * ($gameType->payout)) / 100)/ $game_multiplexer;
//            $targetValue = floor($payout / $gameType->winning_price);
//
////            return response()->json(['success'=>$nextGameDrawObj, 'message' => 'Result added'], 200);
//
//            $result = DB::select(DB::raw("select card_combinations.id as card_combination_id,
//                sum(play_details.quantity) as total_quantity
//                from play_details
//                inner join play_masters ON play_masters.id = play_details.play_master_id
//                inner join card_combinations ON card_combinations.id = play_details.combination_number_id
//                where play_details.game_type_id = 3 and card_combinations.card_combination_type_id = 1 and play_masters.draw_master_id = $lastDrawId and date(play_details.created_at)= " . "'" . $today . "'" . "
//                group by card_combinations.id
//                having sum(play_details.quantity)<= $targetValue
//                order by rand() limit 1"));
//
//            if (empty($result)) {
//                // empty value
//                $result = DB::select(DB::raw("SELECT card_combinations.id as card_combination_id, 0 as total_quantity
//                    FROM card_combinations
//                    WHERE card_combination_type_id = 1 and card_combinations.id NOT IN(SELECT DISTINCT
//                    play_details.combination_number_id FROM play_details
//                    INNER JOIN play_masters on play_details.play_master_id= play_masters.id
//                    WHERE  play_details.game_type_id=3 and DATE(play_masters.created_at) = " . "'" . $today . "'" . " and play_masters.draw_master_id = $lastDrawId)
//                    ORDER by rand() LIMIT 1"));
//            }
//
//            if (empty($result)) {
//                $result = DB::select(DB::raw("select * from (select card_combinations.id as card_combination_id,
//                    sum(play_details.quantity) as total_quantity
//                    from play_details
//                    inner join play_masters ON play_masters.id = play_details.play_master_id
//                    inner join card_combinations ON card_combinations.id = play_details.combination_number_id
//                    where  play_details.game_type_id=3 and card_combination_type_id = 1 and play_masters.draw_master_id = $lastDrawId and date(play_details.created_at)= " . "'" . $today . "'" . "
//                    group by card_combinations.id
//                    having sum(play_details.quantity)>= $targetValue) as table1
//                    order by total_quantity
//                    LIMIT 1"));
//            }
//
//                if($checkMultiplexerStatus == 'yes'){
//                    if(($result[0]->total_quantity) > $targetValue){
//                        $game_multiplexer = 1;
//                    }elseif ($result[0]->total_quantity == 0){
//                        $randNum = rand(0, 10);
//                        $game_multiplexer = $randNum>7 ? $null_multiplexer[array_rand($null_multiplexer,1)] : 1;
//                    }elseif (($result[0]->total_quantity) < $targetValue){
//                        $checkMultiplexer = $result[0]->total_quantity * $gameType->winning_price * 2;
//                        if($checkMultiplexer < $payout){
//                            $game_multiplexer = rand(1,2);
//                        }
//                    }
//                }
//
////                if($checkCount == 0){
////                    $this->checkAndSetDefaultPayout($id);
////                }else{
////                    DB::select("update game_types set counter = (counter - 1) where game_id = ".$id);
////                }
//
//
//            $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,3,$result[0]->card_combination_id,$game_multiplexer))->content(),true);
//
////            if($playMasterSaveCheck['success'] == 0){
////                return response()->json(['success'=>0, 'message' => 'Save error 12 Card'], 401);
////            }
//        }

//        if($id == 3){
//
////            $nextGameDrawObj = NextGameDraw::whereGameId($id)->first();
//            $nextDrawId = $nextGameDrawObj->next_draw_id;
//            $lastDrawId = $nextGameDrawObj->last_draw_id;
//
//            $totalSale = $playMasterControllerObj->get_total_sale($today,$lastDrawId,4);
//            $gameType = GameType::find(4);
//            $payout = (($totalSale * ($gameType->payout)) / 100)/$game_multiplexer;
//            $targetValue = floor($payout / $gameType->winning_price);
//
//            $result = DB::select(DB::raw("select card_combinations.id as card_combination_id,
//                sum(play_details.quantity) as total_quantity
//                from play_details
//                inner join play_masters ON play_masters.id = play_details.play_master_id
//                inner join card_combinations ON card_combinations.id = play_details.combination_number_id
//                where play_details.game_type_id = 4 and card_combinations.card_combination_type_id = 2 and play_masters.draw_master_id = $lastDrawId and date(play_details.created_at)= " . "'" . $today . "'" . "
//                group by card_combinations.id
//                having sum(play_details.quantity)<= $targetValue
//                order by rand() limit 1"));
//
//            if (empty($result)) {
//                // empty value
//                $result = DB::select(DB::raw("SELECT card_combinations.id as card_combination_id, 0 as total_quantity
//                    FROM card_combinations
//                    WHERE card_combination_type_id = 2 and card_combinations.id NOT IN(SELECT DISTINCT
//                    play_details.combination_number_id FROM play_details
//                    INNER JOIN play_masters on play_details.play_master_id= play_masters.id
//                    WHERE  play_details.game_type_id=4 and DATE(play_masters.created_at) = " . "'" . $today . "'" . " and play_masters.draw_master_id = $lastDrawId)
//                    ORDER by rand() LIMIT 1"));
//            }
//
//            if (empty($result)) {
//                $result = DB::select(DB::raw("select * from (select card_combinations.id as card_combination_id,
//                    sum(play_details.quantity) as total_quantity
//                    from play_details
//                    inner join play_masters ON play_masters.id = play_details.play_master_id
//                    inner join card_combinations ON card_combinations.id = play_details.combination_number_id
//                    where  play_details.game_type_id=4 and card_combination_type_id = 2 and play_masters.draw_master_id = $lastDrawId and date(play_details.created_at)= " . "'" . $today . "'" . "
//                    group by card_combinations.id
//                    having sum(play_details.quantity)>= $targetValue) as table1
//                    order by total_quantity
//                    LIMIT 1"));
//            }
//
//            if($checkMultiplexerStatus == 'yes') {
//                if (($result[0]->total_quantity) > $targetValue) {
//                    $game_multiplexer = 1;
//                } elseif ($result[0]->total_quantity == 0) {
//                    $randNum = rand(0, 10);
//                    $game_multiplexer = $randNum > 7 ? $null_multiplexer[array_rand($null_multiplexer, 1)] : 1;
//                } elseif (($result[0]->total_quantity) < $targetValue) {
//                    $checkMultiplexer = $result[0]->total_quantity * $gameType->winning_price * 2;
//                    if ($checkMultiplexer < $payout) {
//                        $game_multiplexer = rand(1, 2);
//                    }
//                }
//            }
//
////            if($checkCount == 0){
////                $this->checkAndSetDefaultPayout($id);
////            }else{
////                DB::select("update game_types set counter = (counter - 1) where game_id = ".$id);
////            }
//
//            $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,4,$result[0]->card_combination_id,$game_multiplexer))->content(),true);
//
////            if($playMasterSaveCheck['success'] == 0){
////                return response()->json(['success'=>0, 'message' => 'Save error 16 Card'], 401);
////            }
//
////            return response()->json(['success'=>1, 'message' => 'Result added'], 200);
//        }

//        if($id == 4){
//
////            $nextGameDrawObj = NextGameDraw::whereGameId($id)->first();
//            $nextDrawId = $nextGameDrawObj->next_draw_id;
//            $lastDrawId = $nextGameDrawObj->last_draw_id;
//
//            $singleNumber = (GameType::find(6));
//
//            $totalSale = $playMasterControllerObj->get_total_sale($today,$lastDrawId,6);
//            $payout = (($totalSale * ($singleNumber->payout)) / 100)/$game_multiplexer;
//            $singleValue = floor($payout / $singleNumber->winning_price);
//
////            $singleValue = (($playMasterControllerObj->get_total_sale($today,$lastDrawId,6) * (($singleNumber->payout)/100))/$game_multiplexer)/($singleNumber->winning_price);
//
////            $singleNumberTargetData = DB::select("select * from play_details
////                inner join play_masters on play_details.play_master_id = play_masters.id
////                where quantity <= ? and game_type_id = 6 and date(play_details.created_at) = ? and play_masters.draw_master_id = ?
////                order by quantity desc
////                limit 1",[$singleValue, $today, $lastDrawId]);
//
//            $singleNumberTargetData = DB::select("select sum(quantity) as quantity,combination_number_id from play_details
//                inner join play_masters on play_details.play_master_id = play_masters.id
//                where play_masters.draw_master_id = ? and play_masters.game_id = 4 and play_details.game_type_id = 6 and date(play_masters.created_at) = ?
//                GROUP by combination_number_id
//                having sum(play_details.quantity)<= ?
//                order by rand()
//                limit 1",[$lastDrawId,$today,$singleValue]);
//
//            //empty check
//            if(empty($singleNumberTargetData)) {
//                $singleNumberTargetData = DB::select("select id as combination_number_id, 0 as quantity from single_numbers
//                    where id not in (select combination_number_id from play_details
//                    inner join play_masters on play_details.play_master_id = play_masters.id
//                    where game_type_id = 6 and date(play_details.created_at) = ? and play_masters.draw_master_id = ?)
//                    order by RAND()
//                    limit 1",[$today, $lastDrawId]);
//            }
//
//            // greater target value
//            if(empty($singleNumberTargetData)){
//                $singleNumberTargetData = DB::select("select * from (select sum(quantity) as quantity,combination_number_id from play_details
//                inner join play_masters on play_details.play_master_id = play_masters.id
//                 where play_masters.draw_master_id = ? and play_masters.game_id = 4 and play_details.game_type_id = 6 and date(play_masters.created_at) = ?
//                GROUP by combination_number_id
//                having sum(play_details.quantity)>= ?) as table1
//                order by quantity
//                LIMIT 1",[$lastDrawId,$today,$singleValue]);
//            }
//
////            if(($singleNumberTargetData[0]->quantity) > $singleValue){
////                $game_multiplexer = 1;
////            }elseif ($singleNumberTargetData[0]->quantity == 0){
////                $randNum = rand(0, 10);
////                $game_multiplexer = $randNum>7 ? $null_multiplexer[array_rand($null_multiplexer,1)] : 1;
////            }
//
//            if($checkMultiplexerStatus == 'yes') {
//                if (($singleNumberTargetData[0]->quantity) > $singleValue) {
//                    $game_multiplexer = 1;
//                } elseif ($singleNumberTargetData[0]->quantity == 0) {
//                    $randNum = rand(0, 10);
//                    $game_multiplexer = $randNum > 7 ? $null_multiplexer[array_rand($null_multiplexer, 1)] : 1;
//                } elseif (($singleNumberTargetData[0]->quantity) < $singleValue) {
//                    $checkMultiplexer = $singleNumberTargetData[0]->quantity * $singleNumber->winning_price * 2;
//                    if ($checkMultiplexer < $payout) {
//                        $game_multiplexer = rand(1, 2);
//                    }
//                }
//            }
//
////            if($checkCount == 0) {
////                $this->checkAndSetDefaultPayout($id);
////            }else{
////                DB::select("update game_types set counter = (counter - 1) where game_id = ".$id);
////            }
//
//            $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,6,$singleNumberTargetData[0]->combination_number_id,$game_multiplexer))->content(),true);
//
////            if($playMasterSaveCheck['success'] == 0){
////                return response()->json(['success'=>0, 'message' => 'Save error single number'], 401);
////            }
//        }

//        if($id == 5){
//
////            $nextGameDrawObj = NextGameDraw::whereGameId($id)->first();
//            $nextDrawId = $nextGameDrawObj->next_draw_id;
//            $lastDrawId = $nextGameDrawObj->last_draw_id;
//
//            $doubleNumber = (GameType::find(7));
//            $andarNumber = (GameType::find(8));
//            $baharNumber = (GameType::find(9));
//
//            $doubleValue = $playMasterControllerObj->get_total_sale($today,$lastDrawId,7);
//            $andarValue = $playMasterControllerObj->get_total_sale($today,$lastDrawId,8);
//            $baharValue = $playMasterControllerObj->get_total_sale($today,$lastDrawId,9);
//
//            $totalDoubleNumberSale = ((int)($doubleValue * ($doubleNumber->payout)/100) + (int)($andarValue * ($andarNumber->payout)/100) + (int)($baharValue * ($baharNumber->payout)/100))/($game_multiplexer);
//
//            $doubleNumberQuantity = (int)($totalDoubleNumberSale/$doubleNumber->winning_price);
//            $loopOn = 1;
//            $gen = 0;
//
//            while (true){
//                $doubleNumberTargetData = DB::select("select * from play_details
//                inner join double_number_combinations on double_number_combinations.id = play_details.combination_number_id
//                inner join play_masters on play_details.play_master_id = play_masters.id
//                where quantity <= ? and game_type_id = 7 and date(play_details.created_at) = ? and play_masters.draw_master_id = ?
//                order by RAND(), quantity desc
//                ",[$doubleNumberQuantity, $today, $lastDrawId]);
//
//                if(!empty($doubleNumberTargetData)){
//                    foreach ($doubleNumberTargetData as $data){
//                        $andarNumberTargetData = DB::select("select ifnull(sum(quantity),0) as quantity from play_details
//                        inner join play_masters on play_details.play_master_id = play_masters.id
//                        where play_details.combination_number_id = ? and game_type_id = 8 and date(play_details.created_at) =  ? and play_masters.draw_master_id = ?
//                        ",[$data->andar_number_id, $today, $lastDrawId]);
//
//                        if(empty($andarNumberTargetData)) {
//                            $andarNumberTargetData = 0;
//                        }else{
//                            $andarNumberTargetData = $andarNumberTargetData[0]->quantity;
//                        }
//
//                        $baharNumberTargetData = DB::select("select ifnull(sum(quantity),0) as quantity from play_details
//                        inner join play_masters on play_details.play_master_id = play_masters.id
//                        where play_details.combination_number_id = ? and game_type_id = 9 and date(play_details.created_at) =  ? and play_masters.draw_master_id = ?
//                        ",[$data->bahar_number_id, $today, $lastDrawId]);
//
//                        if(empty($baharNumberTargetData)) {
//                            $baharNumberTargetData = 0;
//                        }else{
//                            $baharNumberTargetData = $baharNumberTargetData[0]->quantity;
//                        }
//
//                        $totalSale = (($data->quantity) * $doubleNumber->winning_price) + ($andarNumberTargetData * $andarNumber->winning_price) + ($baharNumberTargetData * $baharNumber->winning_price);
//
//                        if($totalSale <= $totalDoubleNumberSale){
//                            $gen = 1;
//                            $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,7,$data->combination_number_id,$game_multiplexer))->content(),true);
//                            $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,8,$data->andar_number_id,$game_multiplexer))->content(),true);
//                            $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,9,$data->bahar_number_id,$game_multiplexer))->content(),true);
////                            return response()->json(['success'=>$data, 'success1' => $totalSale, 'success2'=>$totalDoubleNumberSale], 200);
//                            break;
//                        }
//                    }
//                }else{
//                    break;
//                }
//
//                if($loopOn == 1){
//                    if($doubleNumberQuantity > 0 ){
//                        $doubleNumberQuantity = $doubleNumberQuantity - 1;
//                        continue;
//                    }else{
//                        break;
//                    }
//                }else{
//                    break;
//                }
//            }
//
//            if($gen == 0){
//                if(empty($doubleNumberTargetData)){
//                    $data = $this->checkSmallerTotalSaleForDoubleAndarBahar($lastDrawId);
//                    $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,7,$data->combination_number_id,1))->content(),true);
//                    $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,8,$data->andar_number_id,1))->content(),true);
//                    $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,9,$data->bahar_number_id,1))->content(),true);
//                }
//            }
//
//        }

//        if($id == 6){
//            $nextDrawId = $nextGameDrawObj->next_draw_id;
//            $lastDrawId = $nextGameDrawObj->last_draw_id;
//
//            $rolletNumber = (GameType::find(10));
//
//            $totalSale = $playMasterControllerObj->get_total_sale($today,$lastDrawId,10);
//            $payout = (($totalSale * ($rolletNumber->payout)) / 100)/$game_multiplexer;
//            $targetValue = floor($payout / $rolletNumber->winning_price);
//
//            $rolletNumberTargetData = DB::select("select sum(quantity) as quantity,combination_number_id from play_details
//                inner join play_masters on play_details.play_master_id = play_masters.id
//                where play_masters.draw_master_id = ? and play_masters.game_id = 6 and play_details.game_type_id = 10 and date(play_masters.created_at) = ?
//                GROUP by combination_number_id
//                having sum(play_details.quantity)<= ?
//                order by rand()
//                limit 1",[$lastDrawId,$today,$targetValue]);
//
//            //empty check
//            if(empty($rolletNumberTargetData)) {
//                $rolletNumberTargetData = DB::select("select id as combination_number_id, 0 as quantity from rollet_numbers
//                    where id not in (select combination_number_id from play_details
//                    inner join play_masters on play_details.play_master_id = play_masters.id
//                    where game_type_id = 10 and date(play_details.created_at) = ? and play_masters.draw_master_id = ?)
//                    order by RAND()
//                    limit 1",[$today, $lastDrawId]);
//            }
//
//            // greater target value
//            if(empty($rolletNumberTargetData)){
//                $rolletNumberTargetData = DB::select("select * from (select sum(quantity) as quantity,combination_number_id from play_details
//                inner join play_masters on play_details.play_master_id = play_masters.id
//                 where play_masters.draw_master_id = ? and play_masters.game_id = 6 and play_details.game_type_id = 10 and date(play_masters.created_at) = ?
//                GROUP by combination_number_id
//                having sum(play_details.quantity)>= ?) as table1
//                order by quantity
//                LIMIT 1",[$lastDrawId,$today,$targetValue]);
//            }
//
//            if($checkMultiplexerStatus == 'yes') {
//                if (($rolletNumberTargetData[0]->quantity) > $targetValue) {
//                    $game_multiplexer = 1;
//                } elseif ($rolletNumberTargetData[0]->quantity == 0) {
//                    $randNum = rand(0, 10);
//                    $game_multiplexer = $randNum > 7 ? $null_multiplexer[array_rand($null_multiplexer, 1)] : 1;
//                } elseif (($rolletNumberTargetData[0]->total_quantity) < $targetValue) {
//                    $checkMultiplexer = $rolletNumberTargetData[0]->total_quantity * $rolletNumber->winning_price * 2;
//                    if ($checkMultiplexer < $payout) {
//                        $game_multiplexer = rand(1, 2);
//                    }
//                }
//            }
//
////            if($checkCount == 0){
////               $this->checkAndSetDefaultPayout($id);
////            }else{
////                DB::select("update game_types set counter = (counter - 1) where game_id = ".$id);
////            }
//
//            $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,10,$rolletNumberTargetData[0]->combination_number_id,$game_multiplexer))->content(),true);
//        }


        $resultMaster = ResultMaster::whereGameDate($today)->whereDrawMasterId($lastDrawId)->first();
        if($resultMaster === null){
            if($count < 2){
                $this->createResult($id, 2);
                return 0;
            }
            $nPlay = PlayMaster::whereDrawMasterId($lastDrawId)->whereDate('created_at',$today)->get();
            foreach ($nPlay as $x){
                $playMasterControllerObj->refundPlay($x->id);
            }
        }

        //set payout at default 90
//        $this->checkAndSetDefaultPayout($id,$lastDrawId);


        $tempDrawMasterLastDraw = DrawMaster::whereId($lastDrawId)->whereGameId($id)->first();
        $tempDrawMasterLastDraw->active = 0;
        $tempDrawMasterLastDraw->is_draw_over = 'yes';
        $tempDrawMasterLastDraw->payout = DB::select("select payout from game_types where game_id = ? limit 1",[$id])[0]->payout;
        $tempDrawMasterLastDraw->update();

        if($checkCount == 0){
            $this->checkAndSetDefaultPayout($id);
        }else{
            DB::select("update game_types set counter = (counter - 1) where game_id = ".$id);
        }

        $tempDrawMasterNextDraw = DrawMaster::whereId($nextDrawId)->whereGameId($id)->first();
        $tempDrawMasterNextDraw->active = 1;
        $tempDrawMasterNextDraw->update();

        $totalDraw = DrawMaster::whereGameId($id)->count();
        $gameCountLastDraw = DrawMaster::whereGameId($id)->where('id', '<=', $lastDrawId)->count();
        $gameCountNextDraw = DrawMaster::whereGameId($id)->where('id', '<=', $nextDrawId)->count();

        if($gameCountNextDraw==$totalDraw){
            $nextDrawId = (DrawMaster::whereGameId($id)->first())->id;
        }
        else {
            $nextDrawId = $nextDrawId + 1;
        }

        if($gameCountLastDraw==$totalDraw){
            $lastDrawId = (DrawMaster::whereGameId($id)->first())->id;
        }
        else{
            $lastDrawId = $lastDrawId + 1;
        }

        $nextGameDrawObj = NextGameDraw::whereGameId($id)->first();
        $nextGameDrawObj->next_draw_id = $nextDrawId;
        $nextGameDrawObj->last_draw_id = $lastDrawId;
        $nextGameDrawObj->save();



//        $tempPlayMaster = PlayMaster::select()->where('is_cancelable',1)->whereGameId($id)->get();
//        $chunks = collect($tempPlayMaster)->chunk(100);
//        foreach ($chunks as $chunk){
//            foreach ($chunk as $x){
//                $y = PlayMaster::find($x->id);
//                $y->is_cancelable = 0;
//                $y->update();
//            }
//        }

        DB::select("update play_masters set is_cancelable = 0 where game_id = ".$id);

        DB::select("update game_types set multiplexer = 1 where game_id =  ".$id);

        dispatch(function () {

            $terminalController = new TerminalController();
            $terminalController->claimPrizes();

        })->afterResponse();

//        $terminalController = new TerminalController();
//        $terminalController->claimPrizes();

        return response()->json(['success'=>1, 'message' => 'Result added'], 200);

    }

    public function test(){
        return $this->checkAndSetDefaultPayout(4,2450);

    }

    public function checkAndSetDefaultPayout($id){
//        $gameTypes = DB::select("select id,payout from game_types where game_id = ?",[$id])[0];
//        if(($gameTypes->payout > 100) && ($lastDrawId % 10 == 0)){
//            $gameTypes = GameType::find($gameTypes->id);
//            $gameTypes->payout = 90;
//            $gameTypes->save();
//        }

        DB::select("update game_types set payout = 90 where game_id = ".$id);

    }

    public function getLiveDrawTime(){
        $nextGameDraw = DB::select("select id, last_draw_id,game_id from next_game_draws");
        $x = [
            'game1' => DB::select("select visible_time from draw_masters where id = ?", [(collect($nextGameDraw)->where('game_id', 1)->first())->last_draw_id])[0]->visible_time,
//            'game2' => DB::select("select visible_time from draw_masters where id = ?", [(collect($nextGameDraw)->where('game_id', 2)->first())->last_draw_id])[0]->visible_time,
//            'game3' => DB::select("select visible_time from draw_masters where id = ?", [(collect($nextGameDraw)->where('game_id', 3)->first())->last_draw_id])[0]->visible_time,
//            'game4' => DB::select("select visible_time from draw_masters where id = ?", [(collect($nextGameDraw)->where('game_id', 4)->first())->last_draw_id])[0]->visible_time,
//            'game5' => DB::select("select visible_time from draw_masters where id = ?", [(collect($nextGameDraw)->where('game_id', 5)->first())->last_draw_id])[0]->visible_time,
//            'game6' => DB::select("select visible_time from draw_masters where id = ?", [(collect($nextGameDraw)->where('game_id', 6)->first())->last_draw_id])[0]->visible_time,
        ];
        return response()->json(['success'=>1, 'data' => $x], 200);
    }

    public function testResult(Request $request){
        $requestedData = (object)$request->json()->all();

        $playMasterControllerObj = new PlayMasterController();
        $today= $requestedData->today;
        $lastDrawId = $requestedData->lastDrawId;

        $game_multiplexer = 1;

        $totalSale = $playMasterControllerObj->get_total_sale($today,$lastDrawId,4);

        $gameType = GameType::find(4);
        $payout = (($totalSale * ($gameType->payout)) / 100)/$game_multiplexer;
        $targetValue = floor($payout / $gameType->winning_price);

        $result = DB::select(DB::raw("select card_combinations.id as card_combination_id,
                sum(play_details.quantity) as total_quantity
                from play_details
                inner join play_masters ON play_masters.id = play_details.play_master_id
                inner join card_combinations ON card_combinations.id = play_details.combination_number_id
                where play_details.game_type_id = 4 and card_combinations.card_combination_type_id = 2 and play_masters.draw_master_id = $lastDrawId and date(play_details.created_at)= " . "'" . $today . "'" . "
                group by card_combinations.id
                having sum(play_details.quantity)<= $targetValue
                order by rand() limit 1"));

        if (empty($result)) {
            // empty value
            $result = DB::select(DB::raw("SELECT card_combinations.id as card_combination_id, 0 as total_quantity
                    FROM card_combinations
                    WHERE card_combination_type_id = 2 and card_combinations.id NOT IN(SELECT DISTINCT
                    play_details.combination_number_id FROM play_details
                    INNER JOIN play_masters on play_details.play_master_id= play_masters.id
                    WHERE  play_details.game_type_id=4 and DATE(play_masters.created_at) = " . "'" . $today . "'" . " and play_masters.draw_master_id = $lastDrawId)
                    ORDER by rand() LIMIT 1"));
        }

        if (empty($result)) {
            $result = DB::select(DB::raw("select card_combinations.id as card_combination_id,
                    sum(play_details.quantity) as total_quantity
                    from play_details
                    inner join play_masters ON play_masters.id = play_details.play_master_id
                    inner join card_combinations ON card_combinations.id = play_details.combination_number_id
                    where  play_details.game_type_id=4 and card_combination_type_id = 2 and play_masters.draw_master_id = $lastDrawId and date(play_details.created_at)= " . "'" . $today . "'" . "
                    group by card_combinations.id
                    having sum(play_details.quantity)>= $targetValue
                    order by rand() limit 1"));
        }


//        $playMasterSaveCheck = json_decode(($resultMasterControllerObj->save_auto_result($lastDrawId,6,$singleNumberTargetData[0]->combination_number_id,$game_multiplexer))->content(),true);
        return response()->json(['$totalSale'=>$totalSale, '$singleNumberTargetData' => $result], 200);


    }

    public function checkSmallerTotalSaleForDoubleAndarBahar($last_draw_master_id){
        $today = Carbon::today();
        $result = [];

        $double_winning = GameType::find(7)->winning_price;
        $andar_winning = GameType::find(8)->winning_price;
        $bahar_winning = GameType::find(9)->winning_price;

        $doubleChances = DB::select("select double_number_combinations.id as combination_number_id,double_number_combinations.visible_double_number ,double_number_combinations.andar_number_id , double_number_combinations.bahar_number_id, ifnull(table1.quantity,0) as quantity from
            (select combination_number_id, sum(play_details.quantity) as quantity from play_details
            inner join play_masters on play_details.play_master_id = play_masters.id
            where play_details.game_type_id = 7 and date(play_details.created_at) = ? and play_masters.draw_master_id = ?
            group by combination_number_id) as table1
            right outer join double_number_combinations on table1.combination_number_id = double_number_combinations.id
            order by rand()
            ",[$today,$last_draw_master_id]);

        foreach ($doubleChances as $doubleChance){
            $andar_value = DB::select("Select andar_numbers.id as combination_number_id, ifnull(quantity,0) as quantity from
                (select combination_number_id, sum(quantity) as quantity from play_details
                inner join play_masters on play_details.play_master_id = play_masters.id
                where play_details.game_type_id = 8 and date(play_details.created_at) = ? and play_masters.draw_master_id = ?
                group by combination_number_id) as tabel1
                right outer join andar_numbers on tabel1.combination_number_id = andar_numbers.id
                where andar_numbers.id = ?",[$today,$last_draw_master_id,$doubleChance->andar_number_id])[0];

            $bahar_value = DB::select("Select bahar_numbers.id as combination_number_id, ifnull(quantity,0) as quantity from
                (select combination_number_id, sum(quantity) as quantity from play_details
                inner join play_masters on play_details.play_master_id = play_masters.id
                where play_details.game_type_id = 9 and date(play_details.created_at) = ? and play_masters.draw_master_id = ?
                group by combination_number_id) as tabel1
                right outer join bahar_numbers on tabel1.combination_number_id = bahar_numbers.id
                where bahar_numbers.id = ?",[$today,$last_draw_master_id,$doubleChance->bahar_number_id])[0];

            $calc = ($doubleChance->quantity * $double_winning) + ($andar_value->quantity * $andar_winning) + ($bahar_value->quantity * $bahar_winning);
            $doubleChance->calc = $calc;

            if(count($result) == 0){
                array_push($result, $doubleChance);
            }else{
                if($calc < $result[0]->calc){
                    $result[0] = $doubleChance;
                }
            }
        }

        return $result[0];


    }

    public function checkSmallerTotalSale($last_draw_master_id){

        $single_numbers = Cache::remember('get_all_single_number', 3000000, function () {
            return SingleNumber::select('id','single_number')->get();
        });

        $double_numbers = Cache::remember('get_all_double_number', 3000000, function () {
            return DB::select("select id, single_number_id, double_number, visible_double_number, andar_number_id, bahar_number_id from double_number_combinations");
        });

        $today = Carbon::today();

        $result = [];
        $calc = 0;

        $single_winning = GameType::find(1)->winning_price;
        $double_winning = GameType::find(5)->winning_price;
        $triple_winning = GameType::find(2)->winning_price;

        $tripleChances = DB::select("select number_combinations.id as combination_number_id,number_combinations.visible_triple_number , ifnull(table1.quantity,0) as quantity from
            (select combination_number_id, sum(play_details.quantity) as quantity from play_details
            inner join play_masters on play_details.play_master_id = play_masters.id
            where play_details.game_type_id = 2 and date(play_details.created_at) = ? and play_masters.draw_master_id = ?
            group by combination_number_id) as table1
            right outer join number_combinations on table1.combination_number_id = number_combinations.id
            order by rand()
            ",[$today,$last_draw_master_id]);

        foreach ($tripleChances as $tripleChance){
            $splitNumber = str_split($tripleChance->visible_triple_number);
            $singleNumberValue = (collect($single_numbers)->where('single_number', $splitNumber[2])->first())->id;
            $doubleNumberValue = (collect($double_numbers)->where('double_number', $splitNumber[1].$splitNumber[2])->first())->id;

            $single_value = DB::select("Select single_numbers.id as combination_number_id, ifnull(quantity,0) as quantity from
                (select combination_number_id, sum(quantity) as quantity from play_details
                inner join play_masters on play_details.play_master_id = play_masters.id
                where play_details.game_type_id = 1 and date(play_details.created_at) = ? and play_masters.draw_master_id = ?
                group by combination_number_id) as tabel1
                right outer join single_numbers on tabel1.combination_number_id = single_numbers.id
                where single_numbers.id = ?",[$today,$last_draw_master_id,$singleNumberValue])[0];

            $double_value = DB::select("Select double_number_combinations.id as combination_number_id, ifnull(quantity,0) as quantity from
                (select combination_number_id, sum(quantity) as quantity from play_details
                inner join play_masters on play_details.play_master_id = play_masters.id
                where play_details.game_type_id = 5 and date(play_details.created_at) = ? and play_masters.draw_master_id = ?
                group by combination_number_id) as tabel1
                right outer join double_number_combinations on tabel1.combination_number_id = double_number_combinations.id
                where double_number_combinations.id = ?",[$today,$last_draw_master_id,$doubleNumberValue])[0];

            $calc = ($tripleChance->quantity * $triple_winning) + ($single_value->quantity * $single_winning) + ($double_value->quantity * $double_winning);
            $tripleChance->calc = $calc;

            if(count($result) == 0){
                array_push($result, $tripleChance);
            }else{
                if($calc < $result[0]->calc){
                    $result[0] = $tripleChance;
                }
            }

        }

        return $result;
//        return response()->json(['success'=>1, 'data' => $result], 401);
    }




    public function update_is_draw_over(){
//        $data = DrawMaster::whereIsDrawOver('yes')->get();
//        foreach($data as $x){
//            $y = DrawMaster::find($x->id);
//            $y->is_draw_over = 'no';
//            $y->payout = null;
//            $y->update();
//        }
        DB::select("update draw_masters set is_draw_over = 'no', payout = null");
        return response()->json(['success'=>1], 200);
    }

    public function delete_data_except_thirty_days(){

        $today = Carbon::now()->subDays(42)->format('Y-m-d');

//        DB::select("delete from play_details where date(created_at) = ".$today);
//        DB::select("delete from play_masters where date(created_at) = ".$today);
        DB::select("delete play_masters, play_details
            from play_masters
            inner join play_details on play_masters.id = play_details.play_master_id
            where date(play_masters.created_at) <=  ".$today);
//        DB::select("delete from result_details where date(created_at) = ".$today);
        DB::select("delete result_masters,result_details
            from result_masters
            inner join result_details on result_masters.id = result_details.result_master_id
            where date(game_date) <= ".$today);
//        DB::select("delete from result_masters where date(created_at) = ".$today);
        DB::select("delete from manual_results where date(created_at) = ".$today);
        DB::select("delete from recharge_to_users where date(created_at) = ".$today);
        DB::select("delete from transactions where date(created_at) = ".$today);

        return response()->json(['success'=>1, 'message' => $today], 200);
    }

    public function reset_approve_everyday(){
        DB::select("update users set login_activate = 2 where login_activate = 1");
    }


//    public function createResult(){
//
//        $nextGameDrawObj = NextGameDraw::first();
//        $nextDrawId = $nextGameDrawObj->next_draw_id;
//        $lastDrawId = $nextGameDrawObj->last_draw_id;
//
//        DrawMaster::query()->update(['active' => 0]);
//        if(!empty($nextGameDrawObj)){
//            DrawMaster::findOrFail($nextDrawId)->update(['active' => 1]);
//        }
//
//
//        $resultMasterController = new ResultMasterController();
//        $jsonData = $resultMasterController->save_auto_result($lastDrawId);
//
//        $resultCreatedObj = json_decode($jsonData->content(),true);
//
//        if( !empty($resultCreatedObj) && $resultCreatedObj['success']==1){
//
//            $totalDraw = DrawMaster::count();
//            if($nextDrawId==$totalDraw){
//                $nextDrawId = 1;
//            }
//            else {
//                $nextDrawId = $nextDrawId + 1;
//            }
//
//            if($lastDrawId==$totalDraw){
//                $lastDrawId = 1;
//            }
//            else{
//                $lastDrawId = $lastDrawId + 1;
//            }
//
//            $nextGameDrawObj->next_draw_id = $nextDrawId;
//            $nextGameDrawObj->last_draw_id = $lastDrawId;
//            $nextGameDrawObj->save();
//
//            return response()->json(['success'=>1, 'message' => 'Result added'], 200);
//        }else{
//            return response()->json(['success'=>0, 'message' => 'Result not added'], 401);
//        }
//
//    }

}
