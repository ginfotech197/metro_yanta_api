<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlayGameSave;
use App\Http\Resources\PlayDetailsResource;
use App\Models\GameType;
use App\Models\PlayDetails;
use App\Models\PlayMaster;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayMasterController extends Controller
{

    public function cancelPlay(Request $request)
    {
        $requestedData = (object)$request->json()->all();
        $playMasterId = $requestedData->play_master_id;

        $checkValidation = PlayMaster::whereId($playMasterId)->whereIsCancelable(0)->first();
        if($checkValidation){
            return response()->json(['success' => 0, 'data' => $checkValidation, 'id'=>$checkValidation->id, 'point'=>0], 200);
        }

        $playMaster = new PlayMaster();
        $playMaster = PlayMaster::find($playMasterId);
        $playMaster->is_cancelled = 1;
        $playMaster->is_cancelable = 0;
        $playMaster->update();

        $data = DB::select("select round(sum(play_details.quantity * play_details.mrp)) as total from play_details where play_master_id = ?",[$playMasterId])[0]->total;
//
        $user = new User();
        $user = User::find($playMaster->user_id);
        $old_amount = $user->closing_balance;
        $user->closing_balance += $data;
        $user->update();

        $transaction = new Transaction();
        $transaction->description = 'Cancelled Ticket';
        $transaction->terminal_id = $playMaster->user_id;
        $transaction->play_master_id = $playMaster->id;
        $transaction->old_amount = $old_amount;
        $transaction->played_amount = $data;
        $transaction->new_amount = $user->closing_balance;
        $transaction->save();

        return response()->json(['success' => 1, 'data' => $playMaster, 'id'=>$playMaster->id, 'point'=>$user->closing_balance], 200);
    }

    public function cancelPlayBYPlayMaster($playMasterId)
    {
//        $requestedData = (object)$request->json()->all();
//        $playMasterId = $requestedData->play_master_id;

        $checkValidation = PlayMaster::whereId($playMasterId)->whereIsCancelable(0)->first();
        if($checkValidation){
            return response()->json(['success' => 0, 'data' => $checkValidation, 'id'=>$checkValidation->id, 'point'=>0], 200);
        }

        $playMaster = new PlayMaster();
        $playMaster = PlayMaster::find($playMasterId);
        $playMaster->is_cancelled = 1;
        $playMaster->is_cancelable = 0;
        $playMaster->update();

        $data = DB::select("select round(sum(play_details.quantity * play_details.mrp)) as total from play_details where play_master_id = ?",[$playMasterId])[0]->total;
//
        $user = new User();
        $user = User::find($playMaster->user_id);
        $old_amount = $user->closing_balance;
        $user->closing_balance += $data;
        $user->update();

        $transaction = new Transaction();
        $transaction->description = 'Cancelled Ticket';
        $transaction->terminal_id = $playMaster->user_id;
        $transaction->old_amount = $old_amount;
        $transaction->played_amount = $data;
        $transaction->new_amount = $user->closing_balance;
        $transaction->save();

        return response()->json(['success' => 1, 'data' => $playMaster, 'id'=>$playMaster->id, 'point'=>$user->closing_balance], 200);
    }

    public function refundPlay($playMasterId)
    {
//        $requestedData = (object)$request->json()->all();
//        $playMasterId = $requestedData->play_master_id;

        $checkValidation = PlayMaster::whereId($playMasterId)->whereIsCancelable(0)->first();
        if($checkValidation){
            return response()->json(['success' => 0, 'data' => $checkValidation, 'id'=>$checkValidation->id, 'point'=>0], 200);
        }

        $playMaster = new PlayMaster();
        $playMaster = PlayMaster::find($playMasterId);
        $playMaster->is_cancelled = 1;
        $playMaster->is_cancelable = 0;
        $playMaster->update();

        $data = DB::select("select round(sum(play_details.quantity * play_details.mrp)) as total from play_details where play_master_id = ?",[$playMasterId])[0]->total;
//
        $user = new User();
        $user = User::find($playMaster->user_id);
        $old_amount = $user->closing_balance;
        $user->closing_balance += $data;
        $user->update();

        $transaction = new Transaction();
        $transaction->description = 'Refund';
        $transaction->terminal_id = $playMaster->user_id;
        $transaction->old_amount = $old_amount;
        $transaction->played_amount = $data;
        $transaction->new_amount = $user->closing_balance;
        $transaction->save();

        return response()->json(['success' => 1, 'data' => $playMaster, 'id'=>$playMaster->id, 'point'=>$user->closing_balance], 200);
    }

    public function get_total_quantity($today, $draw_id)
    {

        $gameTypes = GameType::get();
        $temp = [];

        foreach ($gameTypes as $gameType){
            if($gameType->id === 1){
                $temp['single_number'] =  (DB::select("select ifnull(sum(play_details.quantity*play_details.mrp),0) as total_balance from play_details
                inner join play_masters ON play_masters.id = play_details.play_master_id
                where date(play_details.created_at) = ? and play_masters.draw_master_id = ? and play_details.game_type_id = ?", [$today, $draw_id, 1]))[0]->total_balance;
            }

            if($gameType->id === 5){
                $temp['double_number'] =  (DB::select("select ifnull(sum(play_details.quantity*play_details.mrp),0) as total_balance from play_details
                inner join play_masters ON play_masters.id = play_details.play_master_id
                where date(play_details.created_at) = ? and play_masters.draw_master_id = ? and play_details.game_type_id = ?", [$today, $draw_id, 5]))[0]->total_balance;
            }

            if($gameType->id === 2){
                $temp['triple_number'] =  (DB::select("select ifnull(sum(play_details.quantity*play_details.mrp),0) as total_balance from play_details
                inner join play_masters ON play_masters.id = play_details.play_master_id
                where date(play_details.created_at) = ? and play_masters.draw_master_id = ? and play_details.game_type_id = ?", [$today, $draw_id, 2]))[0]->total_balance;
            }
        }

        return $temp;

    }

    public function get_total_sale($today, $draw_id, $gameType)
    {
//        $total = DB::select(DB::raw("select sum(play_details.quantity*play_details.mrp) as total_balance from play_details
//        inner join play_masters ON play_masters.id = play_details.play_master_id
//        where play_masters.draw_master_id = $draw_id  and date(play_details.created_at)= "."'".$today."'"."
//        "));
//
//        if(!empty($total) && isset($total[0]->total_balance) && !empty($total[0]->total_balance)){
//            return $total[0]->total_balance;
//        }else{
//            return 0;
//        }


        $total = DB::select("select ifnull(sum(play_details.quantity*play_details.mrp),0) as total_balance from play_details
        inner join play_masters ON play_masters.id = play_details.play_master_id
        where date(play_details.created_at) = ? and play_masters.draw_master_id = ? and play_details.game_type_id = ?", [$today, $draw_id, $gameType]);

        return $total[0]->total_balance;

    }

    public function get_total_sale_test(Request $request)
    {
        $requestedData = (object)$request->json()->all();
        $today = $requestedData->today;;
        $draw_id = $requestedData->lastDrawId;
        $gameType = $requestedData->gameType;

        $total = DB::select("select ifnull(sum(play_details.quantity*play_details.mrp),0) as total_balance from play_details
        inner join play_masters ON play_masters.id = play_details.play_master_id
        where date(play_details.created_at) = ? and play_masters.draw_master_id = ? and play_details.game_type_id = ?", [$today, $draw_id, $gameType]);

        return $total[0]->total_balance;

    }

    public function get_total_sale_by_terminal($today, $draw_id, $userId)
    {
        $total = 0;
        $total = DB::select("select ifnull(sum(play_details.quantity*play_details.mrp),0) as total_balance from play_details
        inner join play_masters ON play_masters.id = play_details.play_master_id
        where date(play_details.created_at) = ? and  play_masters.draw_master_id = ? and play_masters.user_id = ?", [$today, $draw_id, $userId]);

        if(!empty($total) && isset($total[0]->total_balance) && !empty($total[0]->total_balance)){
            return $total[0]->total_balance;
        }else{
            return 0;
        }
    }

    public function get_total_sale_by_gameType($today, $draw_id, $gameType, $userId)
    {
//        $total = DB::select(DB::raw("select ifnull(sum(play_details.quantity*play_details.mrp),0) as total_balance from play_details
//        inner join play_masters ON play_masters.id = play_details.play_master_id
//        where play_masters.draw_master_id = $draw_id  and date(play_details.created_at)= "."'".$today."'"." and play_details.game_type_id = "."'".$gameType."'"." and play_masters.game_id = "."'".$game."'"."
//        "));

        $total = 0;
        $total = DB::select("select ifnull(sum(play_details.quantity*play_details.mrp),0) as total_balance from play_details
        inner join play_masters ON play_masters.id = play_details.play_master_id
        where date(play_details.created_at) = ? and play_masters.draw_master_id = ? and play_details.game_type_id = ? and play_masters.user_id = ?", [$today, $draw_id, $gameType, $userId]);

        return $total[0]->total_balance;

        if(!empty($total) && isset($total[0]->total_balance) && !empty($total[0]->total_balance)){
            return $total[0]->total_balance;
        }else{
            return 0;
        }
    }

    public function claimPrize(Request $request){
        $requestedData = (object)$request->json()->all();
        $playMasterId = $requestedData->play_master_id;


        $play_master = PlayMaster::find($playMasterId);
        $playMasterDate = Carbon::parse($play_master->created_at)->format('Y-m-d');
        $dateWork = Carbon::createFromDate($playMasterDate);
        $now = Carbon::now();
        $differenceDateCheck = $dateWork->diffInDays($now);
        if($differenceDateCheck > 1){
            return response()->json(['success' => 0, 'message' => 'Failed to claim'], 200,[],JSON_NUMERIC_CHECK);
        }

        $cPanelReportControllerObj = new CPanelReportController();
        $data = $cPanelReportControllerObj->get_prize_value_by_barcode($playMasterId);

        if($data){
            $playMaster = PlayMaster::find($playMasterId);
            $playMaster->is_claimed = 1;
            $playMaster->update();

            if ($playMaster) {
                $user = User::find($playMaster->user_id);
                $old_amount = $user->closing_balance;
                $user->closing_balance = $user->closing_balance + $data;
                $user->update();

                $transaction = Transaction::wherePlayMasterId($playMaster->id)->first();
                if ($transaction) {
                    $transaction->prize_amount = $data;
                    $transaction->new_amount = $user->closing_balance;
                    $transaction->save();
                } else {
                    $transaction = new Transaction();
                    $transaction->terminal_id = $playMaster->user_id;
                    $transaction->play_master_id = $playMaster->id;
                    $transaction->old_amount = $old_amount;
                    $transaction->prize_amount = $data;
                    $transaction->new_amount = $user->closing_balance;
                    $transaction->save();
                }

            }
        }
        return response()->json(['success' => 1, 'point'=>$user->closing_balance], 200);
    }

    public function claimPrizes($id){
//        $requestedData = (object)$request->json()->all();
//        $playMasterId = $requestedData->play_master_id;

        $cPanelReportControllerObj = new CPanelReportController();
        $data = $cPanelReportControllerObj->get_prize_value_by_barcode($id);

        if($data != 0){
//            $playMaster = new PlayMaster();
            $playMaster = PlayMaster::find($id);
            $playMaster->is_claimed = 1;
//            $playMaster->is_cancelable = 1;
            $playMaster->update();

            if($playMaster){
//                $user = new User();
                $user = User::find($playMaster->user_id);
                $old_amount = $user->closing_balance;
                $user->closing_balance += $data;
                $user->update();

                $transaction = Transaction::wherePlayMasterId($playMaster->id)->first();
                $transaction->prize_amount = $data;
                $transaction->new_amount = $user->closing_balance;
                $transaction->save();
            }
        }
        return response()->json(['success' => 1, 'point'=>$user->closing_balance, 'id' =>$playMaster->id], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function get_play_details_by_play_master_id($id){
        $play_details= PlayMaster::findOrFail($id)->play_details;
        return response()->json(['success'=>1,'data'=> PlayDetailsResource::collection($play_details)], 200,[],JSON_NUMERIC_CHECK);
    }

}
