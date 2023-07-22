<?php

namespace App\Http\Controllers;

use App\Http\Resources\StockistResource;
use App\Http\Resources\SuperStockistResource;
use App\Models\CardCombination;
use App\Models\CustomVoucher;
use App\Models\DoubleNumberCombination;
use App\Models\NumberCombination;
use App\Models\PlayMaster;
use App\Models\RechargeToUser;
use App\Models\ResultDetail;
use App\Models\ResultMaster;
use App\Models\RolletNumber;
use App\Models\SingleNumber;
use App\Models\SuperStockist;
use App\Http\Requests\StoreSuperStockistRequest;
use App\Http\Requests\UpdateSuperStockistRequest;
use App\Models\User;
use App\Models\UserRelationWithOther;
use App\Models\UserType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SuperStockistController extends Controller
{
    public function delete_super_stockist_by_admin($id){
        $stockists = DB::select("select stockist_id from user_relation_with_others where super_stockist_id = ".$id);
        $stockistController = new StockistController();

        foreach ($stockists as $stockist){
            $stockistController->delete_stockist_except_admin($stockist->stockist_id);
        }
        DB::select("delete from recharge_to_users where beneficiary_uid = ".$id);

        DB::select("delete from user_relation_with_others where super_stockist_id =  ".$id);

        DB::select("delete from users where id = ".$id);

        Artisan::call('optimize:clear');
        Artisan::call('optimize');

        return response()->json(['success'=>1,'superStockistId' => $id,'message'=> 'Super Stockist Successfully deleted'], 200);
    }


    public function create_super_stockist(Request $request)
    {
        $requestedData = (object)$request->json()->all();

        DB::beginTransaction();
        try{

            $user = new User();
            $user->user_name = $requestedData->userName;
            $user->email = $requestedData->userName;
            $user->password = md5($requestedData->pin);
            $user->visible_password = $requestedData->pin;
            $user->user_type_id = 3;
            $user->created_by = $requestedData->createdBy;
            $user->commission = $requestedData->commission;
            $user->pay_out_slab_id = 1;
            $user->opening_balance = 0;
            $user->closing_balance = 0;
            $user->save();

            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['success'=>0, 'data' => null, 'error'=>$e->getMessage()], 500);
        }

        return response()->json(['success'=>1,'data'=> new SuperStockistResource($user)], 200,[],JSON_NUMERIC_CHECK);
    }

    public function get_super_stockist()
    {
        $data = UserType::find(3)->users;
//        return SuperStockistResource::collection($data);
        return response()->json(['success'=>1,'data'=>SuperStockistResource::collection($data)], 200,[],JSON_NUMERIC_CHECK);
    }

    public function update_balance_to_super_stockist(Request $request){
        $requestedData = (object)$request->json()->all();
        $rules = array(
            'beneficiaryUid'=> ['required',
                function($attribute, $value, $fail){
                    $stockist=User::where('id', $value)->where('user_type_id','=',3)->first();
                    if(!$stockist){
                        return $fail($value.' is not a valid super stockist id');
                    }
                }],
        );
        $messages = array(
            'beneficiaryUid.required' => "Super Stockist required"
        );

        $validator = Validator::make($request->all(),$rules,$messages);
        if ($validator->fails()) {
            return response()->json(['success'=>0, 'data' => $messages], 500);
        }


        DB::beginTransaction();
        try{
            $requestedData = (object)$request->json()->all();
            $beneficiaryUid = $requestedData->beneficiaryUid;
            $amount = $requestedData->amount;

            $beneficiaryObj = User::find($requestedData->rechargeDoneByUid);
            $beneficiaryObj->closing_balance = $beneficiaryObj->closing_balance - $amount;
            $beneficiaryObj->save();

            $beneficiaryObj = User::find($beneficiaryUid);
            $old_amount = $beneficiaryObj->closing_balance;
            $beneficiaryObj->closing_balance = $beneficiaryObj->closing_balance + $amount;
            $beneficiaryObj->save();

            $new_amount = $beneficiaryObj->closing_balance;

            $rechargeToUser = new RechargeToUser();
            $rechargeToUser->beneficiary_uid = $requestedData->beneficiaryUid;
            $rechargeToUser->recharge_done_by_uid = $requestedData->rechargeDoneByUid;
            $rechargeToUser->old_amount = $old_amount;
            $rechargeToUser->amount = $requestedData->amount;
            $rechargeToUser->new_amount = $new_amount;
            $rechargeToUser->save();
            DB::commit();

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['success'=>0, 'data' => null, 'error'=>$e->getMessage()], 500);
        }
        return response()->json(['success'=>1,'data'=> new SuperStockistResource($beneficiaryObj)], 200,[],JSON_NUMERIC_CHECK);
    }

    public function update_super_stockist(Request $request){

        $requestedData = (object)$request->json()->all();

        $user = User::find($requestedData->id);
        $user->user_name = $requestedData->userName;
        $user->email = $requestedData->userName;
        $user->commission = $requestedData->commission;
        $user->password = md5($requestedData->pin);
        $user->visible_password = $requestedData->pin;
        $user->save();


        return response()->json(['success'=>1,'data'=> $user], 200,[],JSON_NUMERIC_CHECK);
    }

    public function getSuperStockistByStockist(Request $request)
    {
        $requestedData = (object)$request->json()->all();
        $data = UserRelationWithOther::whereStockistId($requestedData->stockistId)->first();
        return response()->json(['success'=>1,'data'=> $data], 200,[],JSON_NUMERIC_CHECK);
    }

    public function getStockistBySuperStockistId($id)
    {
        $data = DB::select("select distinct users.id,user_relation_with_others.super_stockist_id, user_relation_with_others.stockist_id, users.user_name from user_relation_with_others
            inner join users on user_relation_with_others.stockist_id = users.id
            where user_relation_with_others.super_stockist_id = ".$id);
        return response()->json(['success'=>1,'data'=> $data], 200,[],JSON_NUMERIC_CHECK);
    }

    public function customer_sale_reports(Request $request){
        $requestedData = (object)$request->json()->all();
        $start_date = $requestedData->startDate;
        $end_date = $requestedData->endDate;
        $userID = $requestedData->userID;

        $cPanelRepotControllerObj = new CPanelReportController();

        // $data = DB::select("select table1.play_master_id, table1.terminal_pin, table1.user_name, table1.user_id, table1.stockist_id, table1.total, table1.commission, users.user_name as stokiest_name from (select max(play_master_id) as play_master_id,terminal_pin,user_name,user_id,stockist_id,
        // sum(total) as total,round(sum(commission),2) as commission from (
        // select max(play_masters.id) as play_master_id,users.user_name,users.email as terminal_pin,
        // round(sum(play_details.quantity * play_details.mrp)) as total,
        // sum(play_details.quantity * play_details.mrp)* (max(play_details.commission)/100) as commission,
        // play_masters.user_id, user_relation_with_others.stockist_id
        // FROM play_masters
        // inner join play_details on play_details.play_master_id = play_masters.id
        // inner join game_types ON game_types.id = play_details.game_type_id
        // inner join users ON users.id = play_masters.user_id
        // left join user_relation_with_others on play_masters.user_id = user_relation_with_others.terminal_id
        // where play_masters.is_cancelled=0 and date(play_masters.created_at) >= ? and date(play_masters.created_at) <= ? and super_stockist_id = ?
        // group by user_relation_with_others.stockist_id, play_masters.user_id,users.user_name,play_details.game_type_id,users.email) as table1 group by user_name,user_id,terminal_pin,stockist_id) as table1
        // left join users on table1.stockist_id = users.id ",[$start_date,$end_date,$userID]);

        $terminals = Cache::remember('allTerminal', 3000000, function () {
            return User::whereUserTypeId(5)->get();
        });

        //        **********************************************************

//        $data = $data = DB::select("select table1.play_master_id,table1.user_id, table1.stockist_id, table1.total, table1.commission,stockist_commission,super_stockist_commission, users.user_name as stokiest_name from (select max(play_master_id) as play_master_id,user_id,stockist_id,
//        sum(total) as total,round(sum(commission),2) as commission,round(sum(stockist_commission),2) as stockist_commission,round(sum(super_stockist_commission),2) as super_stockist_commission from (
//        select max(play_masters.id) as play_master_id,
//        round(sum(play_details.quantity * play_details.mrp)) as total,
//        sum(play_details.quantity * play_details.mrp)* (max(play_details.commission)/100) as commission,
//        sum(play_details.quantity * play_details.mrp)* (max(play_details.stockist_commission)/100) as stockist_commission,
//        sum(play_details.quantity * play_details.mrp)* (max(play_details.super_stockist_commission)/100) as super_stockist_commission,
//        play_masters.user_id, user_relation_with_others.stockist_id
//        FROM play_masters
//        inner join play_details on play_details.play_master_id = play_masters.id
//        inner join game_types ON game_types.id = play_details.game_type_id
//        left join user_relation_with_others on play_masters.user_id = user_relation_with_others.terminal_id
//        where play_masters.is_cancelled=0 and date(play_masters.created_at) >= ? and date(play_masters.created_at) <= ? and user_relation_with_others.active = 1 and super_stockist_id = ?
//        group by user_relation_with_others.stockist_id, play_masters.user_id,play_details.game_type_id) as table1
//        group by user_id,stockist_id) as table1
//        left join users on table1.stockist_id = users.id",[$start_date,$end_date,$userID]);
//
//        foreach($data as $x){
//            $newPrizeClaimed = 0;
//            $newPrizeUnClaimed = 0;
//            $tempntp = 0;
//            $tempPrize = 0;
//            $newData = PlayMaster::where('user_id',$x->user_id)->whereRaw('date(created_at) >= ?', [$start_date])->whereRaw('date(created_at) <= ?', [$end_date])->get();
//            foreach($newData as $y) {
//                $tempData = 0;
//                $tempPrize += $cPanelRepotControllerObj->get_prize_value_by_barcode($y->id);
////                if ($tempPrize > 0 && $y->is_claimed == 1) {
//                if ($tempPrize > 0) {
//                    $newPrizeClaimed += $y->is_claimed == 1? $cPanelRepotControllerObj->get_prize_value_by_barcode($y->id) : 0;
//                    $newPrizeUnClaimed += $y->is_claimed == 0? $cPanelRepotControllerObj->get_prize_value_by_barcode($y->id) : 0;
//                } else {
//                    $newPrizeClaimed += 0;
//                    $newPrizeUnClaimed += 0;
//                }
//            }
//            $detail = (object)$x;
//            $detail->claimed_prize_value = $newPrizeClaimed;
//            $detail->unclaimed_prize_value = $newPrizeUnClaimed;
//            $detail->terminal_pin = (collect($terminals)->where('id', $detail->user_id)->first())->email;
//        }

        //        **********************************************************

//        return response()->json(['success'=> 1, 'data' => $data], 200);
//        return response()->json(['success'=> 1, 'data' => $newData], 200);

        $returnArray = [];
        $users = DB::select("select distinct play_masters.user_id from play_masters
            inner join user_relation_with_others on user_relation_with_others.terminal_id = play_masters.user_id
            where date(play_masters.created_at)>= ? and
            date(play_masters.created_at)<= ? and
            user_relation_with_others.super_stockist_id = ?",[$start_date,$end_date,$userID]);
        foreach ($users as $user){
            $total_sale = 0;
            $terminal_commission = 0;
            $stockist_commission = 0;
            $super_stockist_commission = 0;
            $newPrizeClaimed = 0;
            $newPrizeUnClaimed = 0;

            $newData = PlayMaster::select('id','is_claimed')->whereIsCancelled(0)->where('user_id',$user->user_id)->whereRaw('date(created_at) >= ?', [$start_date])->whereRaw('date(created_at) <= ?', [$end_date])->get();

            foreach ($newData as $x){
                $total_sale = $total_sale + $cPanelRepotControllerObj->total_sale_by_play_master_id($x->id);
                $terminal_commission = $terminal_commission + $cPanelRepotControllerObj->get_terminal_commission($x->id);
                $stockist_commission = $stockist_commission + $cPanelRepotControllerObj->get_stockist_commission_by_play_master_id($x->id);
                $super_stockist_commission = $super_stockist_commission + $cPanelRepotControllerObj->get_super_stockist_commission_by_play_master_id($x->id);
                $newPrizeClaimed += $x->is_claimed == 1? $cPanelRepotControllerObj->get_prize_value_by_barcode($x->id) : 0;
                $newPrizeUnClaimed += $x->is_claimed == 0? $cPanelRepotControllerObj->get_prize_value_by_barcode($x->id) : 0;
            }

            $temp = [
                'user_id' => $user->user_id,
                'total' => $total_sale,
                'commission' => round($terminal_commission, 2),
                'stockist_id' => Cache::remember('customer_sale_reports_admin_stockist_id'.$user->user_id, 3000000, function () use ($user) {
                    return  (UserRelationWithOther::whereTerminalId($user->user_id)->whereActive(1)->first())->stockist_id;
                }),
                'stokiest_name' => Cache::remember('customer_sale_reports_admin_stockist_name'.$user->user_id, 3000000, function () use ($user) {
                    return  (User::select('email')->whereId((UserRelationWithOther::whereTerminalId($user->user_id)->whereActive(1)->first())->stockist_id)->first())->email;
                }),
                'stockist_commission' => round($stockist_commission, 2),
                'super_stockist_commission' => round($super_stockist_commission, 2),
                'claimed_prize_value' => $newPrizeClaimed,
                'unclaimed_prize_value' => $newPrizeUnClaimed,
                'terminal_pin' => (collect($terminals)->where('id', $user->user_id)->first())->email,
            ];

            array_push($returnArray,$temp);
        }

        return response()->json(['success'=> 1, 'data' => $returnArray], 200);
    }

    public function barcode_wise_report_by_date(Request $request){
        $requestedData = (object)$request->json()->all();
        $start_date = $requestedData->startDate;
        $end_date = $requestedData->endDate;
        $userID = $requestedData->userID;

        $cPanelRepotControllerObj = new CPanelReportController();

        $data = PlayMaster::select('play_masters.id as play_master_id', DB::raw('substr(play_masters.barcode_number, 1, 8) as barcode_number')
            ,'draw_masters.visible_time as draw_time','draw_masters.id as draw_master_id','play_masters.created_at','games.id as game_id',
            'users.email as terminal_pin','play_masters.created_at as ticket_taken_time','games.game_name','play_masters.is_claimed', 'games.id as game_id','play_masters.is_cancelled'
        )
            ->join('draw_masters','play_masters.draw_master_id','draw_masters.id')
            ->join('users','users.id','play_masters.user_id')
            ->join('play_details','play_details.play_master_id','play_masters.id')
            ->join('game_types','game_types.id','play_details.game_type_id')
            ->join('games','games.id','game_types.game_id')
            ->join('user_relation_with_others','user_relation_with_others.terminal_id','play_masters.user_id')
//            ->where('play_masters.is_cancelled',0)
            ->where('user_relation_with_others.super_stockist_id',$userID)
            ->whereRaw('date(play_masters.created_at) >= ?', [$start_date])
            ->whereRaw('date(play_masters.created_at) <= ?', [$end_date])
            ->groupBy('play_masters.id','play_masters.barcode_number',
                'draw_masters.visible_time','users.email','play_masters.created_at',
                'games.game_name','play_masters.is_claimed', 'games.id','draw_masters.id','play_masters.is_cancelled')
            ->orderBy('play_masters.created_at','desc')
            ->get();

        foreach($data as $x){
            $detail = (object)$x;

            if((Cache::has(((String)$detail->play_master_id).'result')) == 1){
                $detail->result = Cache::get(((String)$detail->play_master_id).'result');
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
                        $bonus = $resultDetails->multiplexer;
                    }else if($detail->game_id == 2){
                        $resultDetails = ResultDetail::whereResultMasterId($result->id)->whereGameTypeId(3)->first();
                        $x = CardCombination::find($resultDetails->combination_number_id);
                        $showNumber = $x->rank_name. ' ' .$x->suit_name;
                        $bonus = $resultDetails->multiplexer;
                    }else if($detail->game_id == 3){
                        $resultDetails = ResultDetail::whereResultMasterId($result->id)->whereGameTypeId(4)->first();
                        $x = CardCombination::find($resultDetails->combination_number_id);
                        $showNumber = $x->rank_name. ' ' .$x->suit_name;
                        $bonus = $resultDetails->multiplexer;
                    }else if($detail->game_id == 4){
                        $resultDetails = ResultDetail::whereResultMasterId($result->id)->whereGameTypeId(6)->first();
                        $showNumber = (SingleNumber::find($resultDetails->combination_number_id))->single_number;
                        $bonus = $resultDetails->multiplexer;
                    }else if($detail->game_id == 5){
                        $resultDetails = ResultDetail::whereResultMasterId($result->id)->whereGameTypeId(7)->first();
                        $showNumber = (DoubleNumberCombination::find($resultDetails->combination_number_id))->visible_double_number;
                        $bonus = $resultDetails->multiplexer;
                    }else if($detail->game_id == 6){
                        $resultDetails = ResultDetail::whereResultMasterId($result->id)->whereGameTypeId(10)->first();
                        $showNumber = (RolletNumber::find($resultDetails->combination_number_id))->rollet_number;
                    }
                    $detail->result = Cache::remember(((String)$detail->play_master_id).'result', 3000000, function () use ($showNumber) {
                        return $showNumber;
                    });
                    $detail->bonus = Cache::remember(((String)$detail->play_master_id).'bonus', 3000000, function () use ($bonus) {
                        return $bonus;
                    });
                }else{
                    $showNumber = "---";
                    $detail->result = $showNumber;
                    $bonus = "---";
                    $detail->bonus = $bonus;
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

        return response()->json(['success'=> 1, 'data' => $data], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSuperStockistRequest  $request
     * @param  \App\Models\SuperStockist  $superStockist
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSuperStockistRequest $request, SuperStockist $superStockist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SuperStockist  $superStockist
     * @return \Illuminate\Http\Response
     */
    public function destroy(SuperStockist $superStockist)
    {
        //
    }
}
