<?php

namespace App\Http\Controllers;

use App\Models\CardCombination;
use App\Models\DoubleNumberCombination;
use App\Models\DrawMaster;
use App\Models\Game;
use App\Http\Controllers\Controller;
use App\Models\NumberCombination;
use App\Models\PlayDetails;
use App\Models\PlayMaster;
use App\Models\ResultDetail;
use App\Models\ResultMaster;
use App\Models\SingleNumber;
use App\Models\User;
use App\Models\UserRelationWithOther;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class GameController extends Controller
{
    public function getGame()
    {
        $game= Game::get();

        return response()->json(['success'=>1,'data'=> $game], 200,[],JSON_NUMERIC_CHECK);
    }

    public function clearAllCache(){
        Artisan::call('optimize:clear');
        Artisan::call('optimize');

        return response()->json(['success'=>1], 200,[],JSON_NUMERIC_CHECK);
    }

    public function getGameWithTime()
    {
        $today = Carbon::today()->format('Y-m-d');

//        $games = Game::whereId(1)->whereId(2)->whereId(3)->whereId(4)->whereId(5)->get();
        $games = Game::get();
        $commonFunctionController = new CommonFunctionController();
        $serverTime = $commonFunctionController->getServerTime();
        $x = [];
        $temp_arr = [
//            'current_time' => $serverTime
        ];

        foreach ($games as $game){
            $x = [
              'game_id' =>   $game->id,
              'game_name' =>   $game->game_name,
              'draw_id' =>   DrawMaster::whereGameId($game->id)->whereActive(1)->first()->id,
              'draw_time' =>   DrawMaster::whereGameId($game->id)->whereActive(1)->first()->visible_time,
            ];
            array_push($temp_arr, $x);
        }



        return response()->json(['success'=>1,'data'=> $temp_arr, 'current_time' => $serverTime, 'today_date' => $today], 200,[],JSON_NUMERIC_CHECK);
    }

    public function update_auto_generate($id)
    {
        $game= Game::find($id);
        $game->auto_generate= $game->auto_generate=='yes'?'no':'yes';
        $game->update();
        return response()->json(['success'=>1,'data'=> $game], 200,[],JSON_NUMERIC_CHECK);

    }

    public function activate_game($id)
    {
        $game= Game::find($id);
        $game->active= $game->active=='yes'?'no':'yes';
        $game->update();
        return response()->json(['success'=>1,'data'=> $game], 200,[],JSON_NUMERIC_CHECK);
    }

    public function get_game_total_sale_today_stockist($id){
        $terminals = UserRelationWithOther::whereStockistId($id)->whereActive(1)->get();


        $today = Carbon::today()->format('Y-m-d');

        $returnArray = [];
        $triplePrize = 0;
        $twelveCardPrize = 0;
        $sixteenCardPrize = 0;
        $singleNumberPrize = 0;
        $doubleNumberPrize = 0;

        $online_count = 0;

        //variable fol every game
        $totalTripleNumber = 0;
        $twelveCardValue = 0;
        $sixteenCardValue = 0;
        $singleNUmberValue = 0;
        $totalDoubleNumber = 0;
        $twelveCard = 0;
        $sixteenCard = 0;
        $singleNUmber = 0;
        $rolletPrize = 0;
        $totalRollet = 0;


        $CPanelReportController = new CPanelReportController();

        foreach ($terminals as $terminal){

            $onlineCheck = PersonalAccessToken::whereTokenableId($terminal->terminal_id)->whereRaw('date(created_at) = ?', [$today])->first();
            if($onlineCheck){
                $online_count = $online_count + 1;
            }

            $tripleAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereUserId($terminal->terminal_id)->whereGameId(1)->get();
            $twelveCardAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereUserId($terminal->terminal_id)->whereGameId(2)->get();
            $sixteenCardAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereUserId($terminal->terminal_id)->whereGameId(3)->get();
            $singleNumberAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereUserId($terminal->terminal_id)->whereGameId(4)->get();
            $doubleNumberAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereUserId($terminal->terminal_id)->whereGameId(5)->get();
            $rolletAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereUserId($terminal->terminal_id)->whereGameId(6)->get();


            foreach ($tripleAllPlayMasters as $tripleAllPlayMaster){
                $triplePrize = $triplePrize + $CPanelReportController->get_prize_value_by_barcode($tripleAllPlayMaster->id);
                $totalTripleNumber = $totalTripleNumber + $CPanelReportController->total_sale_by_play_master_id($tripleAllPlayMaster->id);
            }

            foreach ($twelveCardAllPlayMasters as $twelveCardAllPlayMaster){
                $twelveCardPrize = $twelveCardPrize + $CPanelReportController->get_prize_value_by_barcode($twelveCardAllPlayMaster->id);
                $twelveCard = $twelveCard + $CPanelReportController->total_sale_by_play_master_id($twelveCardAllPlayMaster->id);
            }

            foreach ($sixteenCardAllPlayMasters as $sixteenCardAllPlayMaster){
                $sixteenCardPrize = $sixteenCardPrize + $CPanelReportController->get_prize_value_by_barcode($sixteenCardAllPlayMaster->id);
                $sixteenCard = $sixteenCard + $CPanelReportController->total_sale_by_play_master_id($sixteenCardAllPlayMaster->id);
            }

            foreach ($singleNumberAllPlayMasters as $singleNumberAllPlayMaster){
                $singleNumberPrize = $singleNumberPrize + $CPanelReportController->get_prize_value_by_barcode($singleNumberAllPlayMaster->id);
                $singleNUmber = $singleNUmber + $CPanelReportController->total_sale_by_play_master_id($singleNumberAllPlayMaster->id);
            }

            foreach ($doubleNumberAllPlayMasters as $doubleNumberAllPlayMaster){
                $doubleNumberPrize = $doubleNumberPrize + $CPanelReportController->get_prize_value_by_barcode($doubleNumberAllPlayMaster->id);
                $totalDoubleNumber = $totalDoubleNumber + $CPanelReportController->total_sale_by_play_master_id($doubleNumberAllPlayMaster->id);
            }

            foreach ($rolletAllPlayMasters as $rolletAllPlayMaster){
                $rolletPrize = $rolletPrize + $CPanelReportController->get_prize_value_by_barcode($rolletAllPlayMaster->id);
                $totalRollet = $totalRollet + $CPanelReportController->total_sale_by_play_master_id($rolletAllPlayMaster->id);
            }


//            $singleNumber = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as single_number from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            inner join play_masters on play_details.play_master_id = play_masters.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 1 and play_masters.user_id = ?
//            group by game_types.mrp",[$today, $terminal->terminal_id]);
//
//            if(!empty($singleNumber)){
//                $singleNumber = $singleNumber[0]->single_number;
//            }else{
//                $singleNumber = 0;
//            }
//
//            $doubleNumber = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as double_number from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            inner join play_masters on play_details.play_master_id = play_masters.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 5 and play_masters.user_id = ?
//            group by game_types.mrp",[$today, $terminal->terminal_id]);
//
//            if(!empty($doubleNumber)){
//                $doubleNumber = $doubleNumber[0]->double_number;
//            }else{
//                $doubleNumber = 0;
//            }
//
//            $tripleNumber = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as triple_number from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            inner join play_masters on play_details.play_master_id = play_masters.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 2 and play_masters.user_id = ?
//            group by game_types.mrp",[$today, $terminal->terminal_id]);
//
//            if(!empty($tripleNumber)){
//                $tripleNumber = $tripleNumber[0]->triple_number;
//            }else{
//                $tripleNumber = 0;
//            }
//
//            $totalTripleNumber = $totalTripleNumber + ($singleNumber + $doubleNumber + $tripleNumber);
//
//            $twelveCard = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as twelve_card from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            inner join play_masters on play_details.play_master_id = play_masters.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 3 and play_masters.user_id = ?
//            group by game_types.mrp",[$today, $terminal->terminal_id]);
//
//            if(!empty($twelveCard)){
//                $twelveCardValue = $twelveCardValue + $twelveCard[0]->twelve_card;
//            }else{
//                $twelveCardValue = $twelveCardValue + 0;
//            }
//
//            $sixteenCard = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as sixteen_card from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            inner join play_masters on play_details.play_master_id = play_masters.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 4 and play_masters.user_id = ?
//            group by game_types.mrp",[$today, $terminal->terminal_id]);
//
//            if(!empty($sixteenCard)){
//                $sixteenCardValue = $sixteenCardValue + $sixteenCard[0]->sixteen_card;
//            }else{
//                $sixteenCardValue = $sixteenCardValue + 0;
//            }
//
//            $singleNUmber = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as single_number from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            inner join play_masters on play_details.play_master_id = play_masters.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 6 and play_masters.user_id = ?
//            group by game_types.mrp",[$today, $terminal->terminal_id]);
//
//            if(!empty($singleNUmber)){
//                $singleNUmberValue = $singleNUmberValue + $singleNUmber[0]->single_number;
//            }else{
//                $singleNUmberValue = $singleNUmberValue + 0;
//            }
//
//            $doubleNumber = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as double_number from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            inner join play_masters on play_details.play_master_id = play_masters.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 7 and play_masters.user_id = ?
//            group by game_types.mrp",[$today, $terminal->terminal_id]);
//
//            if(!empty($doubleNumber)){
//                $doubleNumber = $doubleNumber[0]->double_number;
//            }else{
//                $doubleNumber =  0;
//            }
//
//            $andarNumber = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as andar_number from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            inner join play_masters on play_details.play_master_id = play_masters.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 8 and play_masters.user_id = ?
//            group by game_types.mrp",[$today , $terminal->terminal_id]);
//
//            if(!empty($andarNumber)){
//                $andarNumber = $andarNumber[0]->andar_number;
//            }else{
//                $andarNumber = 0;
//            }
//
//            $baharNumber = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as bahar_number from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 9
//            group by game_types.mrp",[$today]);
//
//            if(!empty($baharNumber)){
//                $baharNumber = $baharNumber[0]->bahar_number;
//            }else{
//                $baharNumber = 0;
//            }
//
//            $totalDoubleNumber = $totalDoubleNumber + ($doubleNumber + $andarNumber + $baharNumber);
        }

        $x = [
            'game_name' => 'Triple Chance',
            'total_bet' =>   $totalTripleNumber,
            'total_win' =>   $triplePrize,
            'profit' =>   $totalTripleNumber - $triplePrize
        ];
        array_push($returnArray , $x);

        $x = [
            'game_name' => '12 Card',
            'total_bet' =>  (int)$twelveCard,
            'total_win' =>   $twelveCardPrize,
            'profit' =>   (int)$twelveCard - $twelveCardPrize
        ];

        array_push($returnArray , $x);

        $x = [
            'game_name' => '16 Card',
            'total_bet' =>  (int)$sixteenCard,
            'total_win' =>   $sixteenCardPrize,
            'profit' =>   (int)$sixteenCard - $sixteenCardPrize
        ];

        array_push($returnArray , $x);

        $x = [
            'game_name' => 'Single Number',
            'total_bet' =>  (int)$singleNUmber,
            'total_win' =>   $singleNumberPrize,
            'profit' =>   (int)$singleNUmber - $singleNumberPrize
        ];

        array_push($returnArray , $x);

        $x = [
            'game_name' => 'Double Number',
            'total_bet' =>  (int)$totalDoubleNumber,
            'total_win' =>   $doubleNumberPrize,
            'profit' =>   (int)$totalDoubleNumber - $doubleNumberPrize
        ];

        array_push($returnArray , $x);

        $x = [
            'game_name' => 'Rollet',
            'total_bet' =>  (int)$totalRollet,
            'total_win' =>   $rolletPrize,
            'profit' =>   (int)$totalRollet - $rolletPrize
        ];

        array_push($returnArray , $x);

        $x = [
            'online' => $online_count
        ];
        array_push($returnArray , $x);

        return response()->json(['success'=>1,'data'=> $returnArray], 200);

    }

    public function stockist_turnover_report(Request $request){
        $requestedData = (object)($request->json()->all());
        $terminals = UserRelationWithOther::whereStockistId($requestedData->stockist_id)->whereActive(1)->get();

        $returnArray = [];
        $totalPrizeClaimed = 0;
        $totalPrizeUnclaimed = 0;
        $totalCommission = 0;

        $totalBet = 0;

        $CPanelReportController = new CPanelReportController();

        foreach ($terminals as $terminal) {
            $allPlayMasters = DB::select("select * from play_masters where date(created_at) >= ? and DATE(created_at) <= ? and user_id = ?",
                [$requestedData->start_date,$requestedData->end_date, $terminal->terminal_id]);

            foreach ($allPlayMasters as $allPlayMaster) {
                $totalPrizeClaimed = $allPlayMaster->is_claimed == 1 ? ($totalPrizeClaimed + $CPanelReportController->get_prize_value_by_barcode($allPlayMaster->id)): $totalPrizeClaimed + 0;
                $totalPrizeUnclaimed = $allPlayMaster->is_claimed == 0 ? ($totalPrizeUnclaimed + $CPanelReportController->get_prize_value_by_barcode($allPlayMaster->id)): $totalPrizeUnclaimed + 0;
                $totalBet = $totalBet + $CPanelReportController->total_sale_by_play_master_id($allPlayMaster->id);
                $totalCommission + $CPanelReportController->get_terminal_commission($allPlayMaster->id);
            }
        }

        $x = [
            'total_bet' =>   $totalBet,
            'total_win_claimed' =>   $totalPrizeClaimed,
            'total_win_unclaimed' =>   $totalPrizeUnclaimed,
            'profit' =>   $totalBet - $totalPrizeClaimed,
            'total_commission' =>  sprintf('%0.2f', $totalCommission),
        ];
        array_push($returnArray , $x);

         return response()->json(['success'=>1,'data'=> $returnArray[0]], 200);
    }

    public function admin_stockist_turnover_report(Request $request){
        $requestedData = (object)($request->json()->all());

        $stockists = DB::select("select id,email from users where user_type_id = 4");
        $returnArray = [];

        foreach ($stockists as $stockist) {
            $terminals = UserRelationWithOther::whereStockistId($stockist->id)->whereActive(1)->get();

            $totalPrizeClaimed = 0;
            $totalPrizeUnclaimed = 0;
            $totalCommission = 0;

            $totalBet = 0;

            $CPanelReportController = new CPanelReportController();

            foreach ($terminals as $terminal) {
                $allPlayMasters = DB::select("select * from play_masters where date(created_at) >= ? and DATE(created_at) <= ? and user_id = ? and is_cancelled = 0",
                    [$requestedData->start_date, $requestedData->end_date, $terminal->terminal_id]);

                foreach ($allPlayMasters as $allPlayMaster) {
                    $totalPrizeClaimed = $allPlayMaster->is_claimed == 1 ? ($totalPrizeClaimed + $CPanelReportController->get_prize_value_by_barcode($allPlayMaster->id)) : $totalPrizeClaimed + 0;
                    $totalPrizeUnclaimed = $allPlayMaster->is_claimed == 0 ? ($totalPrizeUnclaimed + $CPanelReportController->get_prize_value_by_barcode($allPlayMaster->id)) : $totalPrizeUnclaimed + 0;
                    $totalBet = $totalBet + $CPanelReportController->total_sale_by_play_master_id($allPlayMaster->id);
                    $totalCommission + $CPanelReportController->get_terminal_commission($allPlayMaster->id);
                }
            }

            $x = [
                'stockist_id' => $stockist->id,
                'stockist_name' => $stockist->email,
                'total_bet' => $totalBet,
                'total_win_claimed' => $totalPrizeClaimed,
                'total_win_unclaimed' => $totalPrizeUnclaimed,
                'profit' => $totalBet - $totalPrizeClaimed,
                'total_commission' => sprintf('%0.2f', $totalCommission),
            ];
            array_push($returnArray, $x);
        }

        return response()->json(['success'=>1,'data'=> $returnArray], 200);
    }

    public function super_stockist_turnover_report(Request $request){
        $requestedData = (object)($request->json()->all());
        $terminals = UserRelationWithOther::whereSuperStockistId($requestedData->super_stockist_id)->whereActive(1)->get();

        $returnArray = [];
        $totalPrizeClaimed = 0;
        $totalPrizeUnclaimed = 0;
        $totalCommission = 0;

        $totalBet = 0;

        $CPanelReportController = new CPanelReportController();

        foreach ($terminals as $terminal) {
            $allPlayMasters = DB::select("select * from play_masters where date(created_at) >= ? and DATE(created_at) <= ? and user_id = ?",
                [$requestedData->start_date,$requestedData->end_date, $terminal->terminal_id]);

            foreach ($allPlayMasters as $allPlayMaster) {
                $totalPrizeClaimed = $allPlayMaster->is_claimed == 1 ? ($totalPrizeClaimed + $CPanelReportController->get_prize_value_by_barcode($allPlayMaster->id)): $totalPrizeClaimed + 0;
                $totalPrizeUnclaimed = $allPlayMaster->is_claimed == 0 ? ($totalPrizeUnclaimed + $CPanelReportController->get_prize_value_by_barcode($allPlayMaster->id)): $totalPrizeUnclaimed + 0;
                $totalBet = $totalBet + $CPanelReportController->total_sale_by_play_master_id($allPlayMaster->id);
                $totalCommission = $totalCommission + $CPanelReportController->get_terminal_commission($allPlayMaster->id);
            }
        }

        $x = [
            'total_bet' =>   $totalBet,
            'total_win_claimed' =>   $totalPrizeClaimed,
            'total_win_unclaimed' =>   $totalPrizeUnclaimed,
            'profit' =>   $totalBet - $totalPrizeClaimed,
            'total_commission' =>   sprintf('%0.2f', $totalCommission),
        ];
        array_push($returnArray , $x);

         return response()->json(['success'=>1,'data'=> $returnArray[0]], 200);
    }

    public function admin_super_stockist_turnover_report(Request $request){
        $requestedData = (object)($request->json()->all());
        $superStockists = DB::select("select id,email from users where user_type_id = 3");
        $returnArray = [];

        foreach ($superStockists as $superStockist) {
            $terminals = UserRelationWithOther::whereSuperStockistId($superStockist->id)->whereActive(1)->get();


            $totalPrizeClaimed = 0;
            $totalPrizeUnclaimed = 0;
            $totalCommission = 0;

            $totalBet = 0;

            $CPanelReportController = new CPanelReportController();

            foreach ($terminals as $terminal) {
                $allPlayMasters = DB::select("select * from play_masters where date(created_at) >= ? and DATE(created_at) <= ? and user_id = ? and is_cancelled = 0",
                    [$requestedData->start_date, $requestedData->end_date, $terminal->terminal_id]);

                foreach ($allPlayMasters as $allPlayMaster) {
                    $totalPrizeClaimed = $allPlayMaster->is_claimed == 1 ? ($totalPrizeClaimed + $CPanelReportController->get_prize_value_by_barcode($allPlayMaster->id)) : $totalPrizeClaimed + 0;
                    $totalPrizeUnclaimed = $allPlayMaster->is_claimed == 0 ? ($totalPrizeUnclaimed + $CPanelReportController->get_prize_value_by_barcode($allPlayMaster->id)) : $totalPrizeUnclaimed + 0;
                    $totalBet = $totalBet + $CPanelReportController->total_sale_by_play_master_id($allPlayMaster->id);
                    $totalCommission = $totalCommission + $CPanelReportController->get_terminal_commission($allPlayMaster->id);
                }
            }

            $x = [
                'super_stockist_id' => $superStockist->id,
                'super_stockist_name' => $superStockist->email,
                'total_bet' => $totalBet,
                'total_win_claimed' => $totalPrizeClaimed,
                'total_win_unclaimed' => $totalPrizeUnclaimed,
                'profit' => $totalBet - $totalPrizeClaimed,
                'total_commission' => sprintf('%0.2f', $totalCommission),
            ];
            array_push($returnArray, $x);
        }

        return response()->json(['success'=>1,'data'=> $returnArray], 200);
    }

    public function admin_stockist_over_super_stockist_turnover_report(Request $request){
        $requestedData = (object)($request->json()->all());
        $stockists = DB::select("select table1.stockist_id, users.email from (select distinct user_relation_with_others.stockist_id from users
            inner join user_relation_with_others on users.id = user_relation_with_others.super_stockist_id
            where user_relation_with_others.active = 1 and user_relation_with_others.super_stockist_id = ?) as table1
            inner join users on table1.stockist_id = users.id", [$requestedData->super_stockist_id]);
        $returnArray = [];

        foreach ($stockists as $stockist) {
            $terminals = UserRelationWithOther::whereStockistId($stockist->stockist_id)->whereActive(1)->get();

            $totalPrizeClaimed = 0;
            $totalPrizeUnclaimed = 0;
            $totalCommission = 0;
            $totalBet = 0;

            $CPanelReportController = new CPanelReportController();

            foreach ($terminals as $terminal) {

                $allPlayMasters = DB::select("select * from play_masters where date(created_at) >= ? and DATE(created_at) <= ? and user_id = ? and is_cancelled = 0",
                    [$requestedData->start_date, $requestedData->end_date, $terminal->terminal_id]);

                foreach ($allPlayMasters as $allPlayMaster) {
                    $totalPrizeClaimed = $allPlayMaster->is_claimed == 1 ? ($totalPrizeClaimed + $CPanelReportController->get_prize_value_by_barcode($allPlayMaster->id)) : $totalPrizeClaimed + 0;
                    $totalPrizeUnclaimed = $allPlayMaster->is_claimed == 0 ? ($totalPrizeUnclaimed + $CPanelReportController->get_prize_value_by_barcode($allPlayMaster->id)) : $totalPrizeUnclaimed + 0;
                    $totalBet = $totalBet + $CPanelReportController->total_sale_by_play_master_id($allPlayMaster->id);
                    $totalCommission = $totalCommission + $CPanelReportController->get_terminal_commission($allPlayMaster->id);
                }
            }

            $x = [
                'stockist_id' => $stockist->stockist_id,
                'stockist_name' => $stockist->email,
                'total_bet' => $totalBet,
                'total_win_claimed' => $totalPrizeClaimed,
                'total_win_unclaimed' => $totalPrizeUnclaimed,
                'profit' => $totalBet - $totalPrizeClaimed,
                'total_commission' => sprintf('%0.2f', $totalCommission),
            ];
            array_push($returnArray, $x);
        }

        return response()->json(['success'=>1,'data'=> $returnArray], 200);
    }

    public function admin_terminal_over_stockist_turnover_report(Request $request){
        $requestedData = (object)($request->json()->all());
        $terminals = DB::select("select table1.terminal_id, users.email from (select user_relation_with_others.terminal_id from users
            inner join user_relation_with_others on users.id = user_relation_with_others.stockist_id
            where user_relation_with_others.active = 1 and user_relation_with_others.stockist_id = ?) as table1
            inner join users on table1.terminal_id = users.id", [$requestedData->stockist_id]);
        $returnArray = [];

//        foreach ($stockists as $stockist) {
//            $terminals = UserRelationWithOther::whereStockistId($stockist->stockist_id)->whereActive(1)->get();

            $totalPrizeClaimed = 0;
            $totalPrizeUnclaimed = 0;
            $totalCommission = 0;

            $totalBet = 0;

            $CPanelReportController = new CPanelReportController();

            foreach ($terminals as $terminal) {
                $totalPrizeClaimed = 0;
                $totalPrizeUnclaimed = 0;
                $totalBet = 0;
                $totalCommission = 0;

                $allPlayMasters = DB::select("select * from play_masters where date(created_at) >= ? and DATE(created_at) <= ? and user_id = ? and is_cancelled = 0",
                    [$requestedData->start_date, $requestedData->end_date, $terminal->terminal_id]);

                foreach ($allPlayMasters as $allPlayMaster) {
                    $totalPrizeClaimed = $allPlayMaster->is_claimed == 1 ? ($totalPrizeClaimed + $CPanelReportController->get_prize_value_by_barcode($allPlayMaster->id)) : $totalPrizeClaimed + 0;
                    $totalPrizeUnclaimed = $allPlayMaster->is_claimed == 0 ? ($totalPrizeUnclaimed + $CPanelReportController->get_prize_value_by_barcode($allPlayMaster->id)) : $totalPrizeUnclaimed + 0;
                    $totalBet = $totalBet + $CPanelReportController->total_sale_by_play_master_id($allPlayMaster->id);
                    $totalCommission = $totalCommission + $CPanelReportController->get_terminal_commission($allPlayMaster->id);
                }

                $x = [
                    'terminal_id' => $terminal->terminal_id,
                    'terminal_name' => $terminal->email,
                    'total_bet' => $totalBet,
                    'total_win_claimed' => $totalPrizeClaimed,
                    'total_win_unclaimed' => $totalPrizeUnclaimed,
                    'profit' => $totalBet - $totalPrizeClaimed,
                    'total_commission' => sprintf('%0.2f', $totalCommission),
                ];
                array_push($returnArray, $x);
            }
//        }

        return response()->json(['success'=>1,'data'=> $returnArray], 200);
    }

    public function terminal_barcode_report_over_turn_over(Request $request){
        $requestedData = (object)$request->json()->all();

        $start_date = $requestedData->startDate;
        $end_date = $requestedData->endDate;
        $terminalId = $requestedData->terminalId;

        $cpanelReportController = new CPanelReportController();

        $allGame = Cache::remember('allGames', 3000000, function () {
            return Game::get();
        });

        $terminals = Cache::remember('allTerminal', 3000000, function () {
            return User::whereUserTypeId(5)->get();
        });

        $data = PlayMaster::select('play_masters.id as play_master_id', DB::raw('substr(play_masters.barcode_number, 1, 8) as barcode_number')
            ,'play_masters.draw_master_id','play_masters.created_at',
            'play_masters.user_id','play_masters.created_at as ticket_taken_time','play_masters.is_claimed', 'game_types.game_id','play_masters.is_cancelled'
        )
            ->join('play_details','play_details.play_master_id','play_masters.id')
            ->join('game_types','game_types.id','play_details.game_type_id')
            ->whereRaw('date(play_masters.created_at) >= ?', [$start_date])
            ->whereRaw('date(play_masters.created_at) <= ?', [$end_date])
            ->where('play_masters.user_id', [$terminalId])
            ->groupBy('play_masters.id','play_masters.barcode_number','play_masters.created_at',
                'play_masters.is_claimed', 'game_types.game_id','play_masters.draw_master_id','play_masters.user_id','play_masters.is_cancelled')
            ->orderBy('play_masters.created_at','desc')
            ->get();

        foreach($data as $x){
            $detail = (object)$x;

//            $detail->game_name = (collect($allGame)->where('id', $detail->game_id)->first())->game_name;

            $detail->draw_time = Cache::remember('barcode_wise_report_by_date_draw_time_cache'.((String)$detail->play_master_id), 3000000, function () use ($x) {
                return  (DrawMaster::select('visible_time')->whereId($x->draw_master_id)->first())->visible_time;
            });

            $detail->game_name = Cache::remember('barcode_wise_report_by_date_game_cache'.((String)$detail->play_master_id), 3000000, function () use ($detail, $allGame) {
                return  (collect($allGame)->where('id', $detail->game_id)->first())->game_name;
            });

//            $detail->terminal_pin = (collect($terminals)->where('id', $detail->user_id)->first())->email;


            $detail->terminal_pin = Cache::remember('barcode_wise_report_by_date_terminal_pin_cache'.((String)$detail->play_master_id), 3000000, function () use ($detail, $terminals) {
                return  (collect($terminals)->where('id', $detail->user_id)->first())->email;
            });



            if((Cache::has(((String)$detail->play_master_id).'result')) == 1){
                $detail->result = Cache::get(((String)$detail->play_master_id).'result');
//                $detail->bonus = Cache::get(((String)$detail->play_master_id).'bonus');
                if((Cache::get(((String)$detail->play_master_id).'bonus')) == 1){
                    $detail->bonus = Cache::get(((String)$detail->play_master_id).'bonus');
                }else{
                    $result = ResultMaster::whereDrawMasterId($detail->draw_master_id)->whereGameDate($detail->created_at->format('Y-m-d'))->whereGameId($detail->game_id)->first();
                    if($result){
                        $resultDetails = ResultDetail::whereResultMasterId($result->id)->first();
                        $bonus = $resultDetails->multiplexer;
                        if($bonus !== null){
                            $detail->bonus = Cache::remember(((String)$detail->play_master_id).'bonus', 3000000, function () use ($bonus) {
                                return $bonus;
                            });
                        }else{
                            $bonus = "---";
                            $detail->bonus = $bonus;
                        }
                    }
                }
            }else{
                $result = ResultMaster::whereDrawMasterId($detail->draw_master_id)->whereGameDate($detail->created_at->format('Y-m-d'))->whereGameId($detail->game_id)->first();
                if($result){
                    if($detail->game_id == 1){
                        $resultDetails = ResultDetail::whereResultMasterId($result->id)->whereGameTypeId(2)->first();
                        $showNumber = (NumberCombination::find($resultDetails->combination_number_id))->visible_triple_number;
                    }else if($detail->game_id == 2){
                        $resultDetails = ResultDetail::whereResultMasterId($result->id)->whereGameTypeId(3)->first();
                        $x = CardCombination::find($resultDetails->combination_number_id);
                        $showNumber = $x->rank_name. ' ' .$x->suit_name;
                    }else if($detail->game_id == 3){
                        $resultDetails = ResultDetail::whereResultMasterId($result->id)->whereGameTypeId(4)->first();
                        $x = CardCombination::find($resultDetails->combination_number_id);
                        $showNumber = $x->rank_name. ' ' .$x->suit_name;
                    }else if($detail->game_id == 4){
                        $resultDetails = ResultDetail::whereResultMasterId($result->id)->whereGameTypeId(6)->first();
                        $showNumber = (SingleNumber::find($resultDetails->combination_number_id))->single_number;
                    }else if($detail->game_id == 5){
                        $resultDetails = ResultDetail::whereResultMasterId($result->id)->whereGameTypeId(7)->first();
                        $showNumber = (DoubleNumberCombination::find($resultDetails->combination_number_id))->visible_double_number;
                    }
                    $bonus = $resultDetails->multiplexer;
                    $detail->result = Cache::remember(((String)$detail->play_master_id).'result', 3000000, function () use ($showNumber) {
                        return $showNumber;
                    });
                    if($bonus !== null){
                        $detail->bonus = Cache::remember(((String)$detail->play_master_id).'bonus', 3000000, function () use ($bonus) {
                            return $bonus;
                        });
                    }
                }else{
                    $showNumber = "---";
                    $detail->result = $showNumber;
                    $bonus = "---";
                    $detail->bonus = $bonus;
                }
            }

            $detail->total_quantity = Cache::remember(((String)$detail->play_master_id).'total_quantity', 3000000, function () use ($cpanelReportController, $detail) {
                return  $cpanelReportController->get_total_quantity_by_barcode($detail->play_master_id);
            });

            if($detail->is_claimed == 1){
                $detail->prize_value = Cache::remember(((String)$detail->play_master_id).'prize_value', 3000000, function () use ($cpanelReportController, $detail) {
                    return $cpanelReportController->get_prize_value_by_barcode($detail->play_master_id);
                });
            }else{
                $detail->prize_value = $cpanelReportController->get_prize_value_by_barcode($detail->play_master_id);
            }

            $detail->amount = Cache::remember(((String)$detail->play_master_id).'amount', 3000000, function () use ($cpanelReportController, $detail) {
                return $cpanelReportController->get_total_amount_by_barcode($detail->play_master_id);
            });
        }

        return response()->json(['success'=> 1, 'data' => $data], 200,[],JSON_NUMERIC_CHECK);
    }

    public function get_game_total_sale_today_super_stockist($id){
        $terminals = UserRelationWithOther::whereSuperStockistId($id)->whereActive(1)->get();


        $today = Carbon::today()->format('Y-m-d');

        $returnArray = [];
        $triplePrize = 0;
        $twelveCardPrize = 0;
        $sixteenCardPrize = 0;
        $singleNumberPrize = 0;
        $doubleNumberPrize = 0;
        $rolletPrize = 0;
        $totalRollet = 0;

        $online_count = 0;

        //variable fol every game
        $totalTripleNumber = 0;
        $twelveCardValue = 0;
        $sixteenCardValue = 0;
        $singleNUmberValue = 0;
        $totalDoubleNumber = 0;
        $twelveCard = 0;
        $sixteenCard = 0;
        $singleNUmber = 0;


        $CPanelReportController = new CPanelReportController();

        foreach ($terminals as $terminal){

            $onlineCheck = PersonalAccessToken::whereTokenableId($terminal->terminal_id)->whereRaw('date(created_at) = ?', [$today])->first();
            if($onlineCheck){
                $online_count = $online_count + 1;
            }

            $tripleAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereUserId($terminal->terminal_id)->whereGameId(1)->get();
            foreach ($tripleAllPlayMasters as $tripleAllPlayMaster){
                $triplePrize = $triplePrize + $CPanelReportController->get_prize_value_by_barcode($tripleAllPlayMaster->id);
                $totalTripleNumber = $totalTripleNumber + $CPanelReportController->total_sale_by_play_master_id($tripleAllPlayMaster->id);
            }



            $twelveCardAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereUserId($terminal->terminal_id)->whereGameId(2)->get();
            foreach ($twelveCardAllPlayMasters as $twelveCardAllPlayMaster){
                $twelveCardPrize = $twelveCardPrize + $CPanelReportController->get_prize_value_by_barcode($twelveCardAllPlayMaster->id);
                $twelveCard = $twelveCard + $CPanelReportController->total_sale_by_play_master_id($twelveCardAllPlayMaster->id);
            }


            $sixteenCardAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereUserId($terminal->terminal_id)->whereGameId(3)->get();
            foreach ($sixteenCardAllPlayMasters as $sixteenCardAllPlayMaster){
                $sixteenCardPrize = $sixteenCardPrize + $CPanelReportController->get_prize_value_by_barcode($sixteenCardAllPlayMaster->id);
                $sixteenCard = $sixteenCard + $CPanelReportController->total_sale_by_play_master_id($sixteenCardAllPlayMaster->id);
            }



            $singleNumberAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereUserId($terminal->terminal_id)->whereGameId(4)->get();
            foreach ($singleNumberAllPlayMasters as $singleNumberAllPlayMaster){
                $singleNumberPrize = $singleNumberPrize + $CPanelReportController->get_prize_value_by_barcode($singleNumberAllPlayMaster->id);
                $singleNUmber = $singleNUmber + $CPanelReportController->total_sale_by_play_master_id($singleNumberAllPlayMaster->id);
            }



            $doubleNumberAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereUserId($terminal->terminal_id)->whereGameId(5)->get();
            foreach ($doubleNumberAllPlayMasters as $doubleNumberAllPlayMaster){
                $doubleNumberPrize = $doubleNumberPrize + $CPanelReportController->get_prize_value_by_barcode($doubleNumberAllPlayMaster->id);
                $totalDoubleNumber = $totalDoubleNumber + $CPanelReportController->total_sale_by_play_master_id($doubleNumberAllPlayMaster->id);
            }

            $rolletAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereUserId($terminal->terminal_id)->whereGameId(6)->get();
            foreach ($rolletAllPlayMasters as $rolletAllPlayMaster){
                $rolletPrize = $rolletPrize + $CPanelReportController->get_prize_value_by_barcode($rolletAllPlayMaster->id);
                $totalRollet = $totalRollet + $CPanelReportController->total_sale_by_play_master_id($rolletAllPlayMaster->id);
            }




//            $singleNumber = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as single_number from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            inner join play_masters on play_details.play_master_id = play_masters.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 1 and play_masters.user_id = ?
//            group by game_types.mrp",[$today, $terminal->terminal_id]);
//
//            if(!empty($singleNumber)){
//                $singleNumber = $singleNumber[0]->single_number;
//            }else{
//                $singleNumber = 0;
//            }
//
//            $doubleNumber = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as double_number from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            inner join play_masters on play_details.play_master_id = play_masters.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 5 and play_masters.user_id = ?
//            group by game_types.mrp",[$today, $terminal->terminal_id]);
//
//            if(!empty($doubleNumber)){
//                $doubleNumber = $doubleNumber[0]->double_number;
//            }else{
//                $doubleNumber = 0;
//            }
//
//            $tripleNumber = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as triple_number from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            inner join play_masters on play_details.play_master_id = play_masters.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 2 and play_masters.user_id = ?
//            group by game_types.mrp",[$today, $terminal->terminal_id]);
//
//            if(!empty($tripleNumber)){
//                $tripleNumber = $tripleNumber[0]->triple_number;
//            }else{
//                $tripleNumber = 0;
//            }

//            $totalTripleNumber = $totalTripleNumber + ($singleNumber + $doubleNumber + $tripleNumber);

//            $twelveCard = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as twelve_card from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            inner join play_masters on play_details.play_master_id = play_masters.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 3 and play_masters.user_id = ?
//            group by game_types.mrp",[$today, $terminal->terminal_id]);
//
//            if(!empty($twelveCard)){
//                $twelveCardValue = $twelveCardValue + $twelveCard[0]->twelve_card;
//            }else{
//                $twelveCardValue = $twelveCardValue + 0;
//            }

//            $sixteenCard = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as sixteen_card from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            inner join play_masters on play_details.play_master_id = play_masters.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 4 and play_masters.user_id = ?
//            group by game_types.mrp",[$today, $terminal->terminal_id]);
//
//            if(!empty($sixteenCard)){
//                $sixteenCardValue = $sixteenCardValue + $sixteenCard[0]->sixteen_card;
//            }else{
//                $sixteenCardValue = $sixteenCardValue + 0;
//            }
//
//            $singleNUmber = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as single_number from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            inner join play_masters on play_details.play_master_id = play_masters.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 6 and play_masters.user_id = ?
//            group by game_types.mrp",[$today, $terminal->terminal_id]);
//
//            if(!empty($singleNUmber)){
//                $singleNUmberValue = $singleNUmberValue + $singleNUmber[0]->single_number;
//            }else{
//                $singleNUmberValue = $singleNUmberValue + 0;
//            }
//
//            $doubleNumber = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as double_number from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            inner join play_masters on play_details.play_master_id = play_masters.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 7 and play_masters.user_id = ?
//            group by game_types.mrp",[$today, $terminal->terminal_id]);
//
//            if(!empty($doubleNumber)){
//                $doubleNumber = $doubleNumber[0]->double_number;
//            }else{
//                $doubleNumber =  0;
//            }
//
//            $andarNumber = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as andar_number from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            inner join play_masters on play_details.play_master_id = play_masters.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 8 and play_masters.user_id = ?
//            group by game_types.mrp",[$today , $terminal->terminal_id]);
//
//            if(!empty($andarNumber)){
//                $andarNumber = $andarNumber[0]->andar_number;
//            }else{
//                $andarNumber = 0;
//            }
//
//            $baharNumber = DB::select("select ifnull(ifnull(sum(play_details.quantity),0) * game_types.mrp,0) as bahar_number from play_details
//            inner join game_types on play_details.game_type_id = game_types.id
//            where date(play_details.created_at) = ? and play_details.game_type_id = 9
//            group by game_types.mrp",[$today]);
//
//            if(!empty($baharNumber)){
//                $baharNumber = $baharNumber[0]->bahar_number;
//            }else{
//                $baharNumber = 0;
//            }
//
//            $totalDoubleNumber = $totalDoubleNumber + ($doubleNumber + $andarNumber + $baharNumber);
        }

//        return $twelveCard;
//        return response()->json(['success'=>1,'data'=> $twelveCard], 200);

        $x = [
            'game_name' => 'Triple Chance',
            'total_bet' =>   $totalTripleNumber,
            'total_win' =>   $triplePrize,
            'profit' =>   $totalTripleNumber - $triplePrize
        ];
        array_push($returnArray , $x);

        $x = [
            'game_name' => '12 Card',
            'total_bet' =>  (int)$twelveCard,
            'total_win' =>   $twelveCardPrize,
            'profit' =>   (int)$twelveCard - $twelveCardPrize
        ];
        array_push($returnArray , $x);

        $x = [
            'game_name' => '16 Card',
            'total_bet' =>  (int)$sixteenCard,
            'total_win' =>   $sixteenCardPrize,
            'profit' =>   (int)$sixteenCard - $sixteenCardPrize
        ];
        array_push($returnArray , $x);

        $x = [
            'game_name' => 'Single Number',
            'total_bet' =>  (int)$singleNUmber,
            'total_win' =>   $singleNumberPrize,
            'profit' =>   (int)$singleNUmber - $singleNumberPrize
        ];
        array_push($returnArray , $x);

        $x = [
            'game_name' => 'Double Number',
            'total_bet' =>  (int)$totalDoubleNumber,
            'total_win' =>   $doubleNumberPrize,
            'profit' =>   (int)$totalDoubleNumber - $doubleNumberPrize
        ];

        array_push($returnArray , $x);

        $x = [
            'game_name' => 'Rollet',
            'total_bet' =>  (int)$totalRollet,
            'total_win' =>   $rolletPrize,
            'profit' =>   (int)$totalRollet - $rolletPrize
        ];

        array_push($returnArray , $x);

        $x = [
            'online' => $online_count
        ];
        array_push($returnArray , $x);

        return response()->json(['success'=>1,'data'=> $returnArray], 200);

    }

    public function get_game_total_sale_today()
    {
        $today = Carbon::today()->format('Y-m-d');

        $returnArray = [];
        $triplePrize = 0;
        $twelveCardPrize = 0;
        $sixteenCardPrize = 0;
        $singleNumberPrize = 0;
        $doubleNumberPrize = 0;
        $totalTripleNumber = 0;
        $twelveCard = 0;
        $sixteenCard = 0;
        $singleNUmber = 0;
        $totalDoubleNumber = 0;
        $totalRolletNumber = 0;

        $CPanelReportController = new CPanelReportController();

        // triple chance
        $tripleAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereGameId(1)->get();

        $tripleAllPlayMastersSizeCheck = Cache::remember('sizeOfTripleAllPlayMasters_get_game_total_sale_today', 3000000, function () use ($tripleAllPlayMasters) {
            return sizeof($tripleAllPlayMasters);
        });

        if($tripleAllPlayMastersSizeCheck === sizeof($tripleAllPlayMasters) && (Cache::has("tripleAllPlayMastersReturnArray") == 1)){
            $x = Cache::get("tripleAllPlayMastersReturnArray");
            array_push($returnArray , $x);
        }else{
            foreach ($tripleAllPlayMasters as $tripleAllPlayMaster){
                $triplePrize = $triplePrize + $CPanelReportController->get_prize_value_by_barcode($tripleAllPlayMaster->id);
                $totalTripleNumber = $totalTripleNumber + $CPanelReportController->total_sale_by_play_master_id($tripleAllPlayMaster->id);
            }

            $x = [
                'game_name' => 'Triple Chance',
                'total_bet' =>   $totalTripleNumber,
                'total_win' =>   $triplePrize,
                'profit' =>   $totalTripleNumber - $triplePrize
            ];

            array_push($returnArray , $x);
            Cache::forget('tripleAllPlayMastersReturnArray');
            Cache::forget('sizeOfTripleAllPlayMasters_get_game_total_sale_today');
            Cache::remember('sizeOfTripleAllPlayMasters_get_game_total_sale_today', 3000000, function () use ($tripleAllPlayMasters) {
                return sizeof($tripleAllPlayMasters);
            });
            Cache::remember('tripleAllPlayMastersReturnArray', 3000000, function () use ($x) {
                return $x;
            });
        }
        //end of triple chance

        // 12 card
        $twelveCardAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereGameId(2)->get();

        $twelveCardAllPlayMastersSizeCheck = Cache::remember('sizeOfTwelveCardAllPlayMasters_get_game_total_sale_today', 3000000, function () use ($twelveCardAllPlayMasters) {
            return sizeof($twelveCardAllPlayMasters);
        });

        if($twelveCardAllPlayMastersSizeCheck === sizeof($twelveCardAllPlayMasters) && (Cache::has("twelveCardAllPlayMastersReturnArray") == 1)){
            $x = Cache::get("twelveCardAllPlayMastersReturnArray");
            array_push($returnArray , $x);
        }else{
            foreach ($twelveCardAllPlayMasters as $twelveCardAllPlayMaster){
                $twelveCardPrize = $twelveCardPrize + $CPanelReportController->get_prize_value_by_barcode($twelveCardAllPlayMaster->id);
                $twelveCard = $twelveCard + $CPanelReportController->total_sale_by_play_master_id($twelveCardAllPlayMaster->id);
            }

            $x = [
                'game_name' => '12 Card',
                'total_bet' =>  (int)$twelveCard,
                'total_win' =>   $twelveCardPrize,
                'profit' =>   (int)$twelveCard - $twelveCardPrize
            ];
            array_push($returnArray , $x);
            Cache::forget('twelveCardAllPlayMastersReturnArray');
            Cache::forget('sizeOfTwelveCardAllPlayMasters_get_game_total_sale_today');
            Cache::remember('sizeOfTwelveCardAllPlayMasters_get_game_total_sale_today', 3000000, function () use ($twelveCardAllPlayMasters) {
                return sizeof($twelveCardAllPlayMasters);
            });
            Cache::remember('twelveCardAllPlayMastersReturnArray', 3000000, function () use ($x) {
                return $x;
            });
        }
        //end of 12 card

        // 16 card
        $sixteenCardAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereGameId(3)->get();

        $sixteenCardAllPlayMastersSizeCheck = Cache::remember('sizeOfSixteenCardAllPlayMasters_get_game_total_sale_today', 3000000, function () use ($sixteenCardAllPlayMasters) {
            return sizeof($sixteenCardAllPlayMasters);
        });

        if($sixteenCardAllPlayMastersSizeCheck === sizeof($sixteenCardAllPlayMasters) && (Cache::has("sixteenCardAllPlayMastersReturnArray") == 1)){
            $x = Cache::get("sixteenCardAllPlayMastersReturnArray");
            array_push($returnArray , $x);
        }else{
            foreach ($sixteenCardAllPlayMasters as $sixteenCardAllPlayMaster){
                $sixteenCardPrize = $sixteenCardPrize + $CPanelReportController->get_prize_value_by_barcode($sixteenCardAllPlayMaster->id);
                $sixteenCard = $sixteenCard + $CPanelReportController->total_sale_by_play_master_id($sixteenCardAllPlayMaster->id);
            }

            $x = [
                'game_name' => '16 Card',
                'total_bet' =>  (int)$sixteenCard,
                'total_win' =>   $sixteenCardPrize,
                'profit' =>   (int)$sixteenCard - $sixteenCardPrize
            ];
            array_push($returnArray , $x);

            Cache::forget('sixteenCardAllPlayMastersReturnArray');
            Cache::forget('sizeOfSixteenCardAllPlayMasters_get_game_total_sale_today');
            Cache::remember('sizeOfSixteenCardAllPlayMasters_get_game_total_sale_today', 3000000, function () use ($sixteenCardAllPlayMasters) {
                return sizeof($sixteenCardAllPlayMasters);
            });
            Cache::remember('sixteenCardAllPlayMastersReturnArray', 3000000, function () use ($x) {
                return $x;
            });
        }
        //end of 16 card


        // single
        $singleNumberAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereGameId(4)->get();

        $singleNumberAllPlayMastersSizeCheck = Cache::remember('sizeOfSingleNumberAllPlayMasters_get_game_total_sale_today', 3000000, function () use ($singleNumberAllPlayMasters) {
            return sizeof($singleNumberAllPlayMasters);
        });

        if($singleNumberAllPlayMastersSizeCheck === sizeof($singleNumberAllPlayMasters) && (Cache::has("singleNumberAllPlayMastersReturnArray") == 1)){
            $x = Cache::get("singleNumberAllPlayMastersReturnArray");
            array_push($returnArray , $x);
        }else{
            foreach ($singleNumberAllPlayMasters as $singleNumberAllPlayMaster){
                $singleNumberPrize = $singleNumberPrize + $CPanelReportController->get_prize_value_by_barcode($singleNumberAllPlayMaster->id);
                $singleNUmber = $singleNUmber + $CPanelReportController->total_sale_by_play_master_id($singleNumberAllPlayMaster->id);
            }

            $x = [
                'game_name' => 'Single Number',
                'total_bet' =>  (int)$singleNUmber,
                'total_win' =>   $singleNumberPrize,
                'profit' =>   (int)$singleNUmber - $singleNumberPrize
            ];
            array_push($returnArray , $x);

            Cache::forget('singleNumberAllPlayMastersReturnArray');
            Cache::forget('sizeOfSingleNumberAllPlayMasters_get_game_total_sale_today');
            Cache::remember('sizeOfSingleNumberAllPlayMasters_get_game_total_sale_today', 3000000, function () use ($singleNumberAllPlayMasters) {
                return sizeof($singleNumberAllPlayMasters);
            });
            Cache::remember('singleNumberAllPlayMastersReturnArray', 3000000, function () use ($x) {
                return $x;
            });
        }
        // end of single


        // double
        $doubleNumberAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereGameId(5)->get();

        $doubleNumberAllPlayMastersSizeCheck = Cache::remember('sizeOfDoubleNumberAllPlayMasters_get_game_total_sale_today', 3000000, function () use ($doubleNumberAllPlayMasters) {
            return sizeof($doubleNumberAllPlayMasters);
        });

        if($doubleNumberAllPlayMastersSizeCheck === sizeof($doubleNumberAllPlayMasters) && (Cache::has("doubleNumberAllPlayMastersReturnArray") == 1)){
            $x = Cache::get("doubleNumberAllPlayMastersReturnArray");
            array_push($returnArray , $x);
        }else{
            foreach ($doubleNumberAllPlayMasters as $doubleNumberAllPlayMaster){
                $doubleNumberPrize = $doubleNumberPrize + $CPanelReportController->get_prize_value_by_barcode($doubleNumberAllPlayMaster->id);
                $totalDoubleNumber = $totalDoubleNumber + $CPanelReportController->total_sale_by_play_master_id($doubleNumberAllPlayMaster->id);
            }

            $x = [
                'game_name' => 'Double Number',
                'total_bet' =>  (int)$totalDoubleNumber,
                'total_win' =>   $doubleNumberPrize,
                'profit' =>   (int)$totalDoubleNumber - $doubleNumberPrize
            ];
            array_push($returnArray , $x);

            Cache::forget('doubleNumberAllPlayMastersReturnArray');
            Cache::forget('sizeOfDoubleNumberAllPlayMasters_get_game_total_sale_today');
            Cache::remember('sizeOfDoubleNumberAllPlayMasters_get_game_total_sale_today', 3000000, function () use ($doubleNumberAllPlayMasters) {
                return sizeof($doubleNumberAllPlayMasters);
            });
            Cache::remember('doubleNumberAllPlayMastersReturnArray', 3000000, function () use ($x) {
                return $x;
            });
        }
        //end of double

        // rollet
        $rolletAllPlayMasters = PlayMaster::where(DB::raw("date(created_at)"),$today)->whereGameId(6)->get();

        $rolletAllPlayMastersSizeCheck = Cache::remember('sizeOfRolletAllPlayMasters_get_game_total_sale_today', 3000000, function () use ($rolletAllPlayMasters) {
            return sizeof($rolletAllPlayMasters);
        });

        if($rolletAllPlayMastersSizeCheck === sizeof($rolletAllPlayMasters) && (Cache::has("rolletAllPlayMastersReturnArray") == 1)){
            $x = Cache::get("rolletAllPlayMastersReturnArray");
            array_push($returnArray , $x);
        }else{
            foreach ($rolletAllPlayMasters as $rolletAllPlayMaster){
                $triplePrize = $triplePrize + $CPanelReportController->get_prize_value_by_barcode($rolletAllPlayMaster->id);
                $totalRolletNumber = $totalRolletNumber + $CPanelReportController->total_sale_by_play_master_id($rolletAllPlayMaster->id);
            }

            $x = [
                'game_name' => 'Rollet',
                'total_bet' =>   $totalRolletNumber,
                'total_win' =>   $triplePrize,
                'profit' =>   $totalRolletNumber - $triplePrize
            ];

            array_push($returnArray , $x);
            Cache::forget('rolletAllPlayMastersReturnArray');
            Cache::forget('sizeOfRolletAllPlayMasters_get_game_total_sale_today');
            Cache::remember('sizeOfRolletAllPlayMasters_get_game_total_sale_today', 3000000, function () use ($rolletAllPlayMasters) {
                return sizeof($rolletAllPlayMasters);
            });
            Cache::remember('rolletAllPlayMastersReturnArray', 3000000, function () use ($x) {
                return $x;
            });
        }

        $online_count = (DB::select("select COUNT(distinct users.id) as total_count from personal_access_tokens
            inner join users on personal_access_tokens.tokenable_id = users.id
            where date(personal_access_tokens.created_at) = ? and users.user_type_id = 5",[$today]))[0]->total_count;


//        foreach ($tripleAllPlayMasters as $tripleAllPlayMaster){
//            $triplePrize = $triplePrize + $CPanelReportController->get_prize_value_by_barcode($tripleAllPlayMaster->id);
//            $totalTripleNumber = $totalTripleNumber + $CPanelReportController->total_sale_by_play_master_id($tripleAllPlayMaster->id);
//        }

//        foreach ($twelveCardAllPlayMasters as $twelveCardAllPlayMaster){
//            $twelveCardPrize = $twelveCardPrize + $CPanelReportController->get_prize_value_by_barcode($twelveCardAllPlayMaster->id);
//            $twelveCard = $twelveCard + $CPanelReportController->total_sale_by_play_master_id($twelveCardAllPlayMaster->id);
//        }

//        foreach ($sixteenCardAllPlayMasters as $sixteenCardAllPlayMaster){
//            $sixteenCardPrize = $sixteenCardPrize + $CPanelReportController->get_prize_value_by_barcode($sixteenCardAllPlayMaster->id);
//            $sixteenCard = $sixteenCard + $CPanelReportController->total_sale_by_play_master_id($sixteenCardAllPlayMaster->id);
//        }

//        foreach ($singleNumberAllPlayMasters as $singleNumberAllPlayMaster){
//            $singleNumberPrize = $singleNumberPrize + $CPanelReportController->get_prize_value_by_barcode($singleNumberAllPlayMaster->id);
//            $singleNUmber = $singleNUmber + $CPanelReportController->total_sale_by_play_master_id($singleNumberAllPlayMaster->id);
//        }

//        foreach ($doubleNumberAllPlayMasters as $doubleNumberAllPlayMaster){
//            $doubleNumberPrize = $doubleNumberPrize + $CPanelReportController->get_prize_value_by_barcode($doubleNumberAllPlayMaster->id);
//            $totalDoubleNumber = $totalDoubleNumber + $CPanelReportController->total_sale_by_play_master_id($doubleNumberAllPlayMaster->id);
//        }

//        $x = [
//          'game_name' => 'Triple Chance',
//          'total_bet' =>   $totalTripleNumber,
//          'total_win' =>   $triplePrize,
//          'profit' =>   $totalTripleNumber - $triplePrize
//        ];
//        array_push($returnArray , $x);

//        $x = [
//            'game_name' => '12 Card',
//            'total_bet' =>  (int)$twelveCard,
//            'total_win' =>   $twelveCardPrize,
//            'profit' =>   (int)$twelveCard - $twelveCardPrize
//        ];
//        array_push($returnArray , $x);

//        $x = [
//            'game_name' => '16 Card',
//            'total_bet' =>  (int)$sixteenCard,
//            'total_win' =>   $sixteenCardPrize,
//            'profit' =>   (int)$sixteenCard - $sixteenCardPrize
//        ];
//        array_push($returnArray , $x);

//        $x = [
//            'game_name' => 'Single Number',
//            'total_bet' =>  (int)$singleNUmber,
//            'total_win' =>   $singleNumberPrize,
//            'profit' =>   (int)$singleNUmber - $singleNumberPrize
//        ];
//        array_push($returnArray , $x);

//        $x = [
//            'game_name' => 'Double Number',
//            'total_bet' =>  (int)$totalDoubleNumber,
//            'total_win' =>   $doubleNumberPrize,
//            'profit' =>   (int)$totalDoubleNumber - $doubleNumberPrize
//        ];
//        array_push($returnArray , $x);

        $x = [
            'online' => $online_count
        ];
        array_push($returnArray , $x);

        return response()->json(['success'=>1,'data'=> $returnArray], 200);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function edit(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Game $game)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function destroy(Game $game)
    {
        //
    }
}
