<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CardCombination;
use App\Models\DoubleNumberCombination;
use App\Models\NumberCombination;
use App\Models\PlayDetails;
use App\Models\PlayMaster;
use App\Models\ResultDetail;
use App\Models\ResultMaster;
use App\Models\RolletNumber;
use App\Models\SingleNumber;
use App\Models\User;
use App\Models\UserRelationWithOther;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TerminalReportController extends Controller
{
    public function barcode_wise_report_by_terminal(Request $request)
    {

        $requestedData = (object)$request->json()->all();
        $terminalId = $requestedData->terminalId;
        $start_date = $requestedData->startDate;
        $end_date = $requestedData->endDate;

        $data = PlayMaster::select('play_masters.id as play_master_id', DB::raw('substr(play_masters.barcode_number, 1, 8) as barcode_number')
            ,'draw_masters.visible_time as draw_time','draw_masters.id as draw_master_id','play_masters.created_at',
            'users.email as terminal_pin','play_masters.created_at as ticket_taken_time','play_masters.is_cancelled','play_masters.is_cancelable','games.game_name','play_masters.is_claimed',
            DB::raw('games.id as game_id')
        )
            ->join('draw_masters','play_masters.draw_master_id','draw_masters.id')
            ->join('users','users.id','play_masters.user_id')
            ->join('play_details','play_details.play_master_id','play_masters.id')
            ->join('game_types','game_types.id','play_details.game_type_id')
            ->join('games','games.id','game_types.game_id')
            ->whereRaw('date(play_masters.created_at) >= ?', [$start_date])
            ->whereRaw('date(play_masters.created_at) <= ?', [$end_date])
//            ->where('play_masters.is_cancelled',0)
            ->where('play_masters.user_id',$terminalId)
            ->groupBy('play_masters.id','play_masters.barcode_number','play_masters.is_claimed','draw_masters.visible_time','users.email','play_masters.created_at'
                ,'play_masters.is_cancelled','play_masters.is_cancelable','games.game_name','games.id','draw_masters.id','play_masters.is_claimed'
            )
            ->orderBy('play_masters.created_at','desc')
            ->get();

        $cPanelRepotControllerObj = new CPanelReportController();

        foreach($data as $x){
            $detail = (object)$x;

            if((Cache::has(((String)$detail->play_master_id).'result')) == 1){
                $detail->result = Cache::remember(((String)$detail->play_master_id).'result', 3000000, function (){
                });
            }else{
                $result = ResultMaster::whereDrawMasterId($detail->draw_master_id)->whereGameDate($detail->created_at->format('Y-m-d'))->whereGameId($detail->game_id)->first();
                if($result){
                    if($detail->game_id == 1){
                        $resultDetails = ResultDetail::whereResultMasterId($result->id)->whereGameTypeId(1)->first();
                        $showNumber = (SingleNumber::find($resultDetails->combination_number_id))->single_number;
                    }
//                    else if($detail->game_id == 2){
//                        $resultDetails = ResultDetail::whereResultMasterId($result->id)->whereGameTypeId(3)->first();
//                        $x = CardCombination::find($resultDetails->combination_number_id);
//                        $showNumber = $x->rank_name. ' ' .$x->suit_name;
//                    }else if($detail->game_id == 3){
//                        $resultDetails = ResultDetail::whereResultMasterId($result->id)->whereGameTypeId(4)->first();
//                        $x = CardCombination::find($resultDetails->combination_number_id);
//                        $showNumber = $x->rank_name. ' ' .$x->suit_name;
//                    }else if($detail->game_id == 4){
//                        $resultDetails = ResultDetail::whereResultMasterId($result->id)->whereGameTypeId(6)->first();
//                        $showNumber = (SingleNumber::find($resultDetails->combination_number_id))->single_number;
//                    }else if($detail->game_id == 5){
//                        $resultDetails = ResultDetail::whereResultMasterId($result->id)->whereGameTypeId(7)->first();
//                        $showNumber = (DoubleNumberCombination::find($resultDetails->combination_number_id))->visible_double_number;
//                    }else if($detail->game_id == 6){
//                        $resultDetails = ResultDetail::whereResultMasterId($result->id)->whereGameTypeId(10)->first();
//                        $showNumber = (RolletNumber::find($resultDetails->combination_number_id))->rollet_number;
//                    }
                    $detail->result = Cache::remember(((String)$detail->play_master_id).'result', 3000000, function () use ($showNumber) {
                        return $showNumber;
                    });
                }else{
                    $showNumber = "---";
                    $detail->result = $showNumber;
                }
            }

            $detail->total_quantity = Cache::remember(((String)$detail->play_master_id).'total_quantity', 3000000, function () use ($cPanelRepotControllerObj, $detail) {
                return  $cPanelRepotControllerObj->get_total_quantity_by_barcode($detail->play_master_id);
            });

            if($detail->is_claimed == 1){
                $detail->prize_value = Cache::remember(((String)$detail->play_master_id).'prize_value', 3000000, function () use ($cPanelRepotControllerObj, $detail) {
                    return $cPanelRepotControllerObj->get_prize_value_by_barcode($detail->play_master_id);
                });
            }else{
                $detail->prize_value = $cPanelRepotControllerObj->get_prize_value_by_barcode($detail->play_master_id);
            }

            $detail->amount = Cache::remember(((String)$detail->play_master_id).'amount', 3000000, function () use ($cPanelRepotControllerObj, $detail) {
                return $cPanelRepotControllerObj->get_total_amount_by_barcode($detail->play_master_id);
            });

//            $detail->total_quantity = $cPanelRepotControllerObj->get_total_quantity_by_barcode($detail->play_master_id);
//            $detail->prize_value = $cPanelRepotControllerObj->get_prize_value_by_barcode($detail->play_master_id);
//            $detail->amount = $cPanelRepotControllerObj->get_total_amount_by_barcode($detail->play_master_id);
        }

        return response()->json(['success' => 1, 'data' => $data], 200);
    }

    public function terminal_sale_reports(Request $request){

        $requestedData = (object)$request->json()->all();
        $terminalId = $requestedData->terminalId;
        $start_date = $requestedData->startDate;
        $end_date = $requestedData->endDate;

//         //total
        $total_commission = 0;
        $total_sale = 0;
        $total_prize_value = 0;

// //        return $terminalId;

        $cPanelRepotControllerObj = new CPanelReportController();


//         $data = DB::select("select round(table1.commission,2) as commission, table1.total, table1.user_name, users.user_name as stokiest_name, table1.terminal_pin, table1.user_id, table1.stockist_id,
//         table1.`date` from (select sum(commission) as commission, sum(total) as total, user_name, terminal_pin, user_id, stockist_id, date(created_at) as date from (select max(play_masters.id) as play_master_id,users.user_name,users.email as terminal_pin,
//         round(sum(play_details.quantity * play_details.mrp)) as total,
//         sum(play_details.quantity * play_details.mrp)* (max(play_details.commission)/100) as commission,
//         play_masters.user_id, user_relation_with_others.stockist_id,play_masters.created_at
//         FROM play_masters
//         inner join play_details on play_details.play_master_id = play_masters.id
//         inner join game_types ON game_types.id = play_details.game_type_id
//         inner join users ON users.id = play_masters.user_id
//         left join user_relation_with_others on play_masters.user_id = user_relation_with_others.terminal_id
//         where play_masters.is_cancelled=0 and date(play_masters.created_at) >= ? and date(play_masters.created_at) <= ? and user_id = ?
//         group by user_relation_with_others.stockist_id, play_masters.user_id,users.user_name,play_details.game_type_id,users.email,play_masters.created_at) as table1
//         group by terminal_pin, date(created_at), user_name, terminal_pin, user_id, stockist_id) as table1
//         left join users on table1.stockist_id = users.id",[$start_date,$end_date,$terminalId]);

//         foreach($data as $x) {
//             $newPrize = 0;
//             $tempntp = 0;
//             $newData = PlayMaster::whereRaw('date(created_at) >= ?', [$x->date])->where('user_id',$terminalId)->get();
//             foreach ($newData as $y){
// //                $tempData = 0;
//                 $newPrize += $cPanelRepotControllerObj->get_prize_value_by_barcode($y->id);
// //                $tempData = (PlayDetails::select(DB::raw("if(game_type_id = 1,(mrp*22)*quantity-(commission/100),mrp*quantity-(commission/100)) as total"))
// //                    ->where('play_master_id',$y->id)->distinct()->get())[0];
// //                $tempntp += $tempData->total;
//             }
//             $detail = (object)$x;
//             $detail->prize_value = $newPrize;
// //            $detail->ntp = $tempntp;
//         }

//         foreach ($data as $x){
//             $total_commission = $total_commission + $x->commission;
//             $total_sale = $total_sale + $x->total;
//             $total_prize_value = $total_prize_value + $x->prize_value;
//         }

//         $returnArray = [
//             'commission' => sprintf('%0.2f', $total_commission),
//             'total' => $total_sale,
//             'prize_value' => $total_prize_value,
//             'user_name' => $data[0]->user_name,
//             'stokiest_name' => $data[0]->stokiest_name,
//             'terminal_pin' => $data[0]->terminal_pin,
//             'user_id' => $data[0]->user_id,
//             'stockist_id' => $data[0]->stockist_id
//         ];


//         return response()->json(['success' => 1, 'data' => $returnArray], 200,[],JSON_NUMERIC_CHECK);

        $returnArr = [];

        $terminals = Cache::remember('allTerminal', 3000000, function () {
            return User::whereUserTypeId(5)->get();
        });

        $total_sale = 0;
        $terminal_commission = 0;
        $stockist_commission = 0;
        $super_stockist_commission = 0;
        $prize_value = 0;

        $testArr = [];

        $newData = PlayMaster::select('id','is_claimed')->whereIsCancelled(0)->where('user_id',$terminalId)->whereRaw('date(created_at) >= ?', [$start_date])->whereRaw('date(created_at) <= ?', [$end_date])->get();


        foreach ($newData as $x){
            $total_sale = $total_sale + $cPanelRepotControllerObj->total_sale_by_play_master_id($x->id);
            $terminal_commission = $terminal_commission + $cPanelRepotControllerObj->get_terminal_commission($x->id);
            $stockist_commission = $stockist_commission + $cPanelRepotControllerObj->get_stockist_commission_by_play_master_id($x->id);
            $super_stockist_commission = $super_stockist_commission + $cPanelRepotControllerObj->get_super_stockist_commission_by_play_master_id($x->id);
            $prize_value = $prize_value + $cPanelRepotControllerObj->get_prize_value_by_barcode($x->id);
        }

        $temp = [
            'user_id' => $terminalId,
            'total' => $total_sale,
            // 'commission' => round($terminal_commission, 2),
            'commission' => number_format($terminal_commission, 2),
            'stockist_id' => Cache::remember('customer_sale_reports_admin_stockist_id'.$terminalId, 3000000, function () use ($terminalId) {
                return  (UserRelationWithOther::whereTerminalId($terminalId)->whereActive(1)->first())->stockist_id;
            }),
            'stokiest_name' => Cache::remember('customer_sale_reports_admin_stockist_name'.$terminalId, 3000000, function () use ($terminalId) {
                return  (User::select('email')->whereId((UserRelationWithOther::whereTerminalId($terminalId)->whereActive(1)->first())->stockist_id)->first())->email;
            }),
            'stockist_commission' => number_format($stockist_commission, 2),
            'super_stockist_commission' => number_format($super_stockist_commission, 2),
            'prize_value' => $prize_value,
            'terminal_pin' => (collect($terminals)->where('id', $terminalId)->first())->email,
        ];

        array_push($returnArr, $temp);

        return response()->json(['success' => 1, 'data' => $returnArr[0]], 200,[],JSON_NUMERIC_CHECK);
    }





    public function terminal_sale_reports_by_gameId(Request $request){

        $requestedData = (object)$request->json()->all();
        $terminalId = $requestedData->terminalId;
        $start_date = $requestedData->startDate;
        $end_date = $requestedData->endDate;
        $gameId = $requestedData->gameId;

        $cPanelRepotControllerObj = new CPanelReportController();


        $data = DB::select("select round(table1.commission,2) as commission, table1.total, table1.user_name, users.user_name as stokiest_name, table1.terminal_pin, table1.user_id, table1.stockist_id,
        table1.`date` from (select sum(commission) as commission, sum(total) as total, user_name, terminal_pin, user_id, stockist_id, date(created_at) as date from (select max(play_masters.id) as play_master_id,users.user_name,users.email as terminal_pin,
        round(sum(play_details.quantity * play_details.mrp)) as total,
        sum(play_details.quantity * play_details.mrp)* (max(play_details.commission)/100) as commission,
        play_masters.user_id, user_relation_with_others.stockist_id,play_masters.created_at
        FROM play_masters
        inner join play_details on play_details.play_master_id = play_masters.id
        inner join game_types ON game_types.id = play_details.game_type_id
        inner join users ON users.id = play_masters.user_id
        left join user_relation_with_others on play_masters.user_id = user_relation_with_others.terminal_id
        where play_masters.is_cancelled=0 and date(play_masters.created_at) >= ? and date(play_masters.created_at) <= ? and user_id = ?
        and play_masters.game_id = ?
        group by user_relation_with_others.stockist_id, play_masters.user_id,users.user_name,play_details.game_type_id,users.email,play_masters.created_at) as table1
        group by terminal_pin, date(created_at), user_name, terminal_pin, user_id, stockist_id) as table1
        left join users on table1.stockist_id = users.id",[$start_date,$end_date,$terminalId, $gameId]);

        foreach($data as $x) {
            $newPrize = 0;
            $tempntp = 0;
            $newData = PlayMaster::whereRaw('date(created_at) >= ?', [$x->date])->where('user_id',$terminalId)->get();
            foreach ($newData as $y){
//                $tempData = 0;
                $newPrize += $cPanelRepotControllerObj->get_prize_value_by_barcode($y->id);
//                $tempData = (PlayDetails::select(DB::raw("if(game_type_id = 1,(mrp*22)*quantity-(commission/100),mrp*quantity-(commission/100)) as total"))
//                    ->where('play_master_id',$y->id)->distinct()->get())[0];
//                $tempntp += $tempData->total;
            }
            $detail = (object)$x;
            $detail->prize_value = $newPrize;
//            $detail->ntp = $tempntp;
        }


        return response()->json(['success' => 1, 'data' => $data, JSON_NUMERIC_CHECK], 200);
    }




    public function updateCancellation(){
        $data = PlayMaster::select()->where('is_cancelable',1)->get();
        foreach ($data as $x){
            $y = PlayMaster::find($x->id);
            $y->is_cancelable = 0;
            $y->update();
        }
        return response()->json(['success' => 1, 'data' => $data], 200);
    }

    public function updateCancellationGameWise($id){
        $data = PlayMaster::select()->where('is_cancelable',1)->whereGameId($id)->get();
        foreach ($data as $x){
            $y = PlayMaster::find($x->id);
            $y->is_cancelable = 0;
            $y->update();
        }
        return response()->json(['success' => 1, 'data' => $data], 200);
    }
}
