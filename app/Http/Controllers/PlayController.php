<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlayDetailsResource;
use App\Http\Resources\PlayMasterResource;
use App\Http\Resources\PrintSingleGameInputResource;
use App\Http\Resources\PrintTripleGameInputResource;
use App\Models\GameAllocation;
use App\Models\GameType;
use App\Models\PayOutSlab;
use App\Models\PlayDetails;
use App\Models\PlayMaster;
use App\Models\SingleNumber;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserRelationWithOther;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Litespeed\LSCache\LSCache;

class PlayController extends Controller
{
    public function save_play_details(Request $request)
    {
        $requestedData = $request->json()->all();

        $inputPlayMaster = (object)$requestedData['playMaster'];
        $inputPlayDetails = $requestedData['playDetails'];
//        $gameAllocation = GameAllocation::whereUserId($inputPlayMaster->terminalId)->first();
//        $gameName = ('game'.$inputPlayMaster->gameId);
//
//        if($gameAllocation->$gameName == 0){
//            return response()->json(['success'=>0,'data'=>null, 'message' => 'Game not Allocated'], 406,[],JSON_NUMERIC_CHECK);
//        }

        $today= Carbon::today()->format('Y-m-d');

        $drawMasterId = DB::select("select id from draw_masters where game_id = $inputPlayMaster->gameId and active = 1")[0]->id;
        $resultMasterDrawId = DB::select("select * from result_masters where date(created_at) = ".$today." and draw_master_id = ".$drawMasterId);

        if($resultMasterDrawId){
            return response()->json(['success'=> 0, 'data' => null, "message" => "Please buy ticket on ".Carbon::today()->addDays(1)->format('d-m-Y')." on this draw"], 200);
        }

        $userRelationId = UserRelationWithOther::whereTerminalId($inputPlayMaster->terminalId)->whereActive(1)->first();
        $payoutSlabValue = (PayOutSlab::find((User::find($inputPlayMaster->terminalId))->pay_out_slab_id))->slab_value;
        $user = User::find($inputPlayMaster->terminalId);

        $output_array = array();


        DB::beginTransaction();
        try{

            $playMaster = new PlayMaster();
//            $playMaster->draw_master_id = $inputPlayMaster->drawMasterId;
            $playMaster->draw_master_id = $drawMasterId;
            $playMaster->barcode_number = rand(10000000000000000,99999999999999999);
            $playMaster->user_id = $inputPlayMaster->terminalId;
            $playMaster->game_id = $inputPlayMaster->gameId;
//            $playMaster->user_relation_id = $userRelationId->id;
            $playMaster->user_relation_id = 1;
            $playMaster->save();
            $output_array['play_master'] = new PlayMasterResource($playMaster);


            $tempItems = collect($inputPlayDetails);
            $items = ($tempItems->chunk(650)->toArray());

            $ps_commission = User::find((UserRelationWithOther::whereTerminalId($inputPlayMaster->terminalId)->whereActive(1)->first())->stockist_id)->commission;
            $pss_commission = User::find((UserRelationWithOther::whereTerminalId($inputPlayMaster->terminalId)->whereActive(1)->first())->super_stockist_id)->commission;


            foreach ($items as $item){
                foreach($item as $inputPlayDetail){
                    $detail = (object)$inputPlayDetail;
//                    $gameType = GameType::find($detail->gameTypeId);
                    $gameTypes = Cache::remember('buyTicketGameTypes', 40, function () {
                        return GameType::select('id','mrp','payout','multiplexer')->get();
                    });
                    $gameType = collect($gameTypes)->where('id', 1)->first();
//                    $gameType = collect($gameTypes)->where('id', $detail->gameTypeId)->first();

                    //insert value for triple
//                    if($detail->gameTypeId == 1){

                        if($detail->singleNumberId == 0){
                            continue;
                        }

                        $playDetails = new PlayDetails();
                        $playDetails->play_master_id = $playMaster->id;
                        $playDetails->game_type_id = 1;
                        $playDetails->combination_number_id = $detail->singleNumberId;
                        $playDetails->quantity = $detail->quantity;
//                        $playDetails->mrp = round($detail->mrp/22,4);
                        $playDetails->mrp = $gameType->mrp;
                        $playDetails->commission = $user->commission;
                        $playDetails->ps_commission = $ps_commission;
                        $playDetails->series_id = 1;
//                        $playDetails->series_id = $detail->series_id;
                        $playDetails->stockist_commission = $playDetails->ps_commission - $user->commission;
                        $playDetails->pss_commission = $pss_commission;
                        $playDetails->super_stockist_commission = $playDetails->pss_commission - $playDetails->ps_commission;
                        $playDetails->global_payout = $gameType->payout;
                        $playDetails->terminal_payout = $payoutSlabValue;
                        $playDetails->combined_number = 1;
//                        $playDetails->multiplexer = $gameType->multiplexer;
                        $playDetails->save();
                    }

//                }
            }

//            $amount = $playMaster->play_details->sum(function($t){
//                return  $t->quantity * $t->mrp;
//            });


                $amount = $playMaster->play_details->sum(function($t){
                    return  $t->quantity * $t->mrp;
                });



//            $userClosingBalance = (User::find($inputPlayMaster->terminalId))->closing_balance;
            $userClosingBalance = $user->closing_balance;

            if($userClosingBalance < $amount){
                DB::rollBack();
                return response()->json(['success'=>0,'data'=> null, 'message' => 'Low balance'], 200,[],JSON_NUMERIC_CHECK);
            }

            $output_array['amount'] = round($amount,0);

//            $terminal = User::findOrFail($inputPlayMaster->terminalId);
//            $old_amount = $terminal->closing_balance;
//            $terminal->closing_balance-= $amount;
//            $terminal->save();

//            $terminal = User::findOrFail($inputPlayMaster->terminalId);
            $old_amount = $user->closing_balance;
            $user->closing_balance-= $amount;
            $user->save();

            $transaction = new Transaction();
            $transaction->description = 'Purchased';
            $transaction->terminal_id = $inputPlayMaster->terminalId;
            $transaction->play_master_id = $playMaster->id;
            $transaction->old_amount = $old_amount;
            $transaction->played_amount = $amount;
            $transaction->new_amount = $user->closing_balance;
            $transaction->save();

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json(['success'=>0,'exception'=>$e->getMessage(),'error_line'=>$e->getLine(),'file_name' => $e->getFile()], 500);
        }

        return response()->json(['success'=>1,'data'=> $output_array], 200,[],JSON_NUMERIC_CHECK);
    }

    public function get_game_input_details_by_play_master_id($play_master_id){
        $output_array = array();
        $single_game_data = PlayDetails::select(DB::raw('max(single_numbers.single_number) as single_number')
            ,DB::raw('max(play_details.quantity) as quantity'))
            ->join('number_combinations','play_details.combination_number_id','number_combinations.id')
            ->join('single_numbers','number_combinations.single_number_id','single_numbers.id')
            ->where('play_details.play_master_id',$play_master_id)
            ->where('play_details.game_type_id',1)
            ->groupBy('single_numbers.id')
            ->orderBy('single_numbers.single_order')
            ->get();
        $output_array['single_game_data'] = PrintSingleGameInputResource::collection($single_game_data);

        $triple_game_data = PlayDetails::select('number_combinations.visible_triple_number','play_details.quantity',
            'single_numbers.single_number')
            ->join('number_combinations','play_details.combination_number_id','number_combinations.id')
            ->join('single_numbers','number_combinations.single_number_id','single_numbers.id')
            ->where('play_details.play_master_id',$play_master_id)
            ->where('play_details.game_type_id',2)
            ->orderBy('single_numbers.single_order')
            ->get();
        $output_array['triple_game_data'] = PrintTripleGameInputResource::collection($triple_game_data);

        return $output_array;
    }
}
