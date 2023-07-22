<?php

namespace App\Http\Controllers;
use App\Models\GameAllocation;
use App\Models\PlayMaster;
use App\Models\Transaction;
use App\Models\UserRelationWithOther;
use App\Models\UserType;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\RechargeToUser;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\CustomVoucher;

use App\Http\Controllers\Controller;
use App\Http\Resources\TerminalResource;
use App\Models\StockistToTerminal;
use Illuminate\Http\Request;
/////// for log
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;


class TerminalController extends Controller
{
    public function get_all_terminals(){
//        $terminals = UserType::find(5)->users;
//        $terminals = User::select()->whereUserTypeId(5)
//            ->join('user_relation_with_others','users.id','user_relation_with_others.terminal_id')
//            ->get();
        $terminals = DB::select("select users.id, users.login_activate,users.platform , users.visible_password ,users.blocked, users.user_name,users.version,
       users.email,users.pay_out_slab_id ,users.password, users.commission ,users.remember_token, users.mobile1, users.user_type_id, users.opening_balance,
       users.closing_balance, users.created_by, users.inforce, user_relation_with_others.super_stockist_id, user_relation_with_others.stockist_id,
       user_relation_with_others.terminal_id, user_relation_with_others.changed_by, user_relation_with_others.active, user_relation_with_others.end_date,
       user_relation_with_others.changed_for, users.auto_claim from users
            inner join user_relation_with_others on users.id = user_relation_with_others.terminal_id
            where user_relation_with_others.active = 1");
        return TerminalResource::collection($terminals);
//        return $terminals;
    }

    public function delete_terminal_by_admin($id){

        DB::select("delete from personal_access_tokens WHERE tokenable_id = ".$id);

        DB::select("delete play_masters,play_details
            from play_masters
            inner join play_details on play_masters.id = play_details.play_master_id
            where play_masters.user_id = ".$id);

        DB::select("delete from transactions where terminal_id = ".$id);

        DB::select("update user_relation_with_others set terminal_id = null where terminal_id = ".$id);

        DB::select("delete FROM game_allocations where user_id = ".$id);

        DB::select("delete from users where id = ".$id);

        Artisan::call('optimize:clear');
        Artisan::call('optimize');

        return response()->json(['success'=>1,'message'=> 'Terminal Successfully deleted'], 200);
    }

    public function delete_terminal_except_admin($id){

        DB::select("delete from personal_access_tokens WHERE tokenable_id = ".$id);

        DB::select("delete play_masters,play_details
            from play_masters
            inner join play_details on play_masters.id = play_details.play_master_id
            where play_masters.user_id = ".$id);

        DB::select("delete from transactions where terminal_id = ".$id);

        DB::select("delete from recharge_to_users where beneficiary_uid = ".$id);

        DB::select("update user_relation_with_others set terminal_id = null where terminal_id = ".$id);

        DB::select("delete FROM game_allocations where user_id = ".$id);

        DB::select("delete from users where id = ".$id);



        return response()->json(['success'=>1], 200);
    }

    public function get_logged_in_terminal($id){
        $user = User::find($id);

        if($user->user_type_id != 5){
            return response()->json(['success'=>0,'data'=>null, 'message' => 'Invalid terminal'], 200);
        }

        return response()->json(['success'=>1,'data'=>new TerminalResource($user)], 200);
    }

    public function get_logged_in_terminal_balance($id){
        $user = DB::select("select closing_balance from users where id = ?",[$id])[0];

        return response()->json(['success'=>1,'balance'=>$user->closing_balance], 200);
    }

    public function is_user_logged_in($id){
        $token = PersonalAccessToken::whereTokenableId($id)->first();

        if($token){
            return response()->json(['success'=>1], 200);
        }

        return response()->json(['success'=>0], 200);
    }

    public function force_logout_terminal($id){
        $data = DB::select("delete from personal_access_tokens where tokenable_id = ?",[$id]);
        return response()->json(['success'=>1], 200);
    }


    public function get_terminal_by_auth(Request $request){
        return TerminalResource::collection($request->user());
    }

    public function claimAllPrizes($x){
//        User::select('id')->whereAutoClaim(1)->whereUserTypeId(5)->chunk(300, function ($users){

//            foreach ($users as $x){
                $prize_value = 0;
//                $y = PlayMaster::whereUserId($x->id)->whereIsClaimed(0)->whereIsCancelled(0)->get();
                PlayMaster::select('id')->whereUserId($x)->whereIsClaimed(0)->whereIsCancelled(0)->where( 'created_at', '>', Carbon::now()->subDays(2)->format('Y-m-d'))->chunk(200, function ($y) {

                    if ($y) {
                        foreach ($y as $z) {

                            if ($z !== []) {

                                $cPanelReportControllerObj = new CPanelReportController();
                                $data = $cPanelReportControllerObj->get_prize_value_by_barcode($z->id);

                                if ($data != 0) {
                                    $playMaster = PlayMaster::find($z->id);
                                    $playMaster->is_claimed = 1;
                                    $playMaster->update();

                                    if ($playMaster) {
                                        $user = User::find($playMaster->user_id);
                                        $old_amount = $user->closing_balance;
                                        $user->closing_balance = $user->closing_balance + $data;
                                        $user->update();

                                        $transaction = Transaction::wherePlayMasterId($z->id)->first();
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

                            }
                        }
                    }
                });

//            }

//        });

        return response()->json(['success'=>1], 200);
    }

    public function claimPrizes(){

        $two_days = Carbon::now()->subDays(1)->format('Y-m-d');

        User::select('id')->whereAutoClaim(1)->whereUserTypeId(5)->chunk(300, function ($users,$two_days){

            foreach ($users as $x){
                $prize_value = 0;
//                $y = PlayMaster::whereUserId($x->id)->whereIsClaimed(0)->whereIsCancelled(0)->get();
                PlayMaster::select('id')->whereUserId($x->id)->whereIsClaimed(0)->whereIsCancelled(0)->whereRaw('date(play_masters.created_at) >= ?', [$two_days])->chunk(200, function ($y) {

                    if ($y) {
                        foreach ($y as $z) {

                            if ($z !== []) {

                                $cPanelReportControllerObj = new CPanelReportController();
                                $data = $cPanelReportControllerObj->get_prize_value_by_barcode($z->id);

                                if ($data != 0) {
                                    $playMaster = PlayMaster::select('id','is_claimed','user_id')->find($z->id);
                                    $playMaster->is_claimed = 1;
                                    $playMaster->update();

                                    if ($playMaster) {
                                        $user = User::find($playMaster->user_id);
                                        $old_amount = $user->closing_balance;
                                        $user->closing_balance = $user->closing_balance + $data;
                                        $user->update();

                                        $transaction = Transaction::wherePlayMasterId($z->id)->first();
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

                            }
                        }
                    }
                });

            }

        });

//        foreach ($users as $x){
//            $prize_value = 0;
//            $y = PlayMaster::whereUserId($x->id)->whereIsClaimed(0)->whereIsCancelled(0)->get();
//
//
//            if($y){
//                foreach ($y as $z){
//
//                    if($z !== []){
//
//                        $cPanelReportControllerObj = new CPanelReportController();
//                        $data = $cPanelReportControllerObj->get_prize_value_by_barcode($z->id);
//                        $temp1 = [
//                            'z' => $z,
//                            'data' => $data,
//                        ];
//
//                        if($data != 0){
//                            $playMaster = PlayMaster::find($z->id);
//                            $playMaster->is_claimed = 1;
//                            $playMaster->update();
//
//                            if($playMaster){
//                                $user = User::find($playMaster->user_id);
//                                $old_amount = $user->closing_balance;
//                                $user->closing_balance = $user->closing_balance + $data;
//                                $user->update();
//
//                                $transaction = Transaction::wherePlayMasterId($z->id)->first();
//                                if($transaction){
//                                    $transaction->prize_amount = $data;
//                                    $transaction->new_amount = $user->closing_balance;
//                                    $transaction->save();
//                                }else{
//                                    $transaction = new Transaction();
//                                    $transaction->terminal_id = $playMaster->user_id;
//                                    $transaction->play_master_id = $playMaster->id;
//                                    $transaction->old_amount = $old_amount;
//                                    $transaction->prize_amount = $data;
//                                    $transaction->new_amount = $user->closing_balance;
//                                    $transaction->save();
//                                }
//
//                            }
//                        }
//
//                    }
//                }
//            }
//
//        }
        return response()->json(['success'=>1], 200);
    }

    public function update_auto_claim($id){
        $user = User::find($id);

        if($user->user_type_id != 5){
            return response()->json(['success'=>0,'data'=>null, 'message' => 'Invalid terminal'], 200);
        }

        $getUser = User::find($user->id);
        $getUser->auto_claim = ($getUser->auto_claim == 0)? 1 : 0;
        $getUser->save();


        return response()->json(['success'=>1,'data'=>new TerminalResource($getUser)], 200);
    }

    public function prize_value_by_terminal_id(Request $request){
//        $user = $request->user();
        $requestedData = (object)$request->json()->all();
        $data = 0;

        $today= Carbon::today()->format('Y-m-d');

        $playMasters =  DB::select("select * from play_masters where date(created_at) = ? and is_cancelled = 0 and draw_master_id = ? and user_id = ".$requestedData->id,[$today, $requestedData->draw_master_id]);

        foreach ($playMasters as $x){
            $cpanelReportController =  new CPanelReportController();
            $data = $data + $cpanelReportController->get_prize_value_by_barcode($x->id);
        }

        return response()->json(['success'=>1,'data'=>$data], 200);
    }

//    public function draw_wise_report(Request $request){
////        $requestedData = (object)$request->json()->all();
////        $gameId = $requestedData->game_id;
//        $today= Carbon::today()->format('Y-m-d');
//        $test = 0;
//        $total_prize = 0;
//        $total_quantity = 0;
//
//        $data = DB::select("select play_masters.id, play_masters.barcode_number, play_masters.draw_master_id, play_masters.user_id, play_masters.game_id,
//       play_masters.user_relation_id, play_masters.is_claimed, play_masters.is_cancelled, play_masters.is_cancelable, play_masters.created_at, play_masters.updated_at,
//       draw_masters.draw_name, draw_masters.visible_time from play_masters
//             inner join draw_masters ON draw_masters.id = play_masters.draw_master_id
//             where date(play_masters.created_at) = ? and play_masters.game_id = 1",[$today]);
//
//        $cpanelReportController =  new CPanelReportController();
//        foreach ($data as $x){
//            $total_prize = $total_prize + (int)$cpanelReportController->get_prize_value_by_barcode($x->id);
//            $total_quantity = $total_quantity + $cpanelReportController->get_total_quantity_by_barcode($x->id);
//        }
//
//        return response()->json(['success'=> $total_prize, 'data' => $total_quantity], 200);
//    }

    public function save_notification_message(Request $request){
        $requestedData = ((object)$request->json()->all())->message;
        $cacheAppVer = Cache::forever('message_notification', $requestedData);
        return response()->json(['success'=> 1, 'data' => $requestedData], 200);
    }

    public function get_notification_message(){
        $cacheAppVer = Cache::get('message_notification');
        return response()->json(['success'=> 1, 'data' => $cacheAppVer], 200);
    }

    public function delete_user($id){
        DB::select("delete from transactions where terminal_id = $id");
        DB::select("delete from recharge_to_users where beneficiary_uid = $id");
        DB::select("delete from user_relation_with_others WHERE terminal_id = $id");
        DB::select("delete play_details,play_masters
            from play_details
            inner join play_masters on play_masters.id = play_details.play_master_id
            where play_masters.user_id = $id");
        DB::select("delete from game_allocations where user_id = $id");
        DB::select("delete from users where id = $id");

        return response()->json(['success'=>1], 200);
    }

    public function approve_login(Request $request){
        $requestedData = (object)$request->json()->all();

        $user = User::find($requestedData->id);

//        $macReset = User::whereMacAddress($user->temp_mac_address)->first();
//        $macReset->mac_address = 0;
//        $macReset->save();

        if($user->mac_address != 'null'){
            DB::select("update users set mac_address = '' where mac_address = ?", [$user->mac_address]);
        }

        $user->mac_address = $user->temp_mac_address;
        $user->login_activate = 2;
        $user->save();

        DB::select("delete from personal_access_tokens where tokenable_id = ".$requestedData->id);

        return response()->json(['success'=>1,'data'=>new TerminalResource($user)], 200);
    }

    public function game_permission_update(Request $request){
        $requestedData = (object)$request->json()->all();
        $gameName = ('game'.$requestedData->gameId);

        $gameAllocation = GameAllocation::whereUserId($requestedData->terminalId)->first();
        $gameAllocation->$gameName = ($gameAllocation->$gameName == 0) ? 1 : 0;
        $gameAllocation->save();

        $user = User::find($requestedData->terminalId);

        return response()->json(['success'=>1,'data'=>new TerminalResource($user)], 200);
    }


    // public function get_stockist_by_terminal_id(){
    //     $trminals = User::find(StockistToTerminal::whereTerminalId(14)->first()->stockist_id);
    //     return response()->json(['success'=>0, 'data' => $trminals], 500);
    // }



    public function create_terminal(Request $request){
        $requestedData = (object)$request->json()->all();

        DB::beginTransaction();
        try{

            $user = new User();
            $user->user_name = $requestedData->terminalName;
            $user->email = $requestedData->terminalName;
            $user->password = md5($requestedData->pin);
            $user->visible_password = $requestedData->pin;
            $user->user_type_id = 5;
            $user->created_by = $requestedData->createdBy;
            $user->pay_out_slab_id = 2;
            $user->commission = $requestedData->commission;
            $user->opening_balance = 0;
            $user->closing_balance = 0;
            $user->save();

            $userRelation = UserRelationWithOther::whereStockistId($requestedData->stockistId)->whereTerminalId(null)->first();

            if($userRelation){
//                $userRelation->super_stockist_id = $requestedData->superStockistId;
                $userRelation->stockist_id = $requestedData->stockistId;
                $userRelation->terminal_id = $user->id;
                $userRelation->save();
            }else{
                $userRelationNew = new UserRelationWithOther();
                $userRelationNew->super_stockist_id = $requestedData->superStockistId;
                $userRelationNew->stockist_id = $requestedData->stockistId;
                $userRelationNew->terminal_id = $user->id;
                $userRelationNew->save();
            }

            $gameAllocation = new GameAllocation();
            $gameAllocation->user_id = $user->id;
            $gameAllocation->game1 = $requestedData->game1;
            $gameAllocation->game2 = $requestedData->game2;
            $gameAllocation->game3 = $requestedData->game3;
            $gameAllocation->game4 = $requestedData->game4;
            $gameAllocation->game5 = $requestedData->game5;

            $gameAllocation->save();

            Cache::forget('allTerminal');
            Cache::remember('allTerminal', 3000000, function () {
                return User::whereUserTypeId(5)->get();
            });

            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['success'=>0, 'data' => null, 'error'=>$e->getMessage()], 500);
        }

        return response()->json(['success'=>1,'data'=> new TerminalResource($user)], 200,[],JSON_NUMERIC_CHECK);
    }


    public function update_terminal(Request $request){

        $requestedData = (object)$request->json()->all();

//        $checkValidation = UserRelationWithOther::whereSuperStockistId(5)->whereStockistId(6)->whereTerminalId(null)->first();
//
//        if(!$checkValidation){
//            return response()->json(['success'=>0,'data'=>$checkValidation], 200,[],JSON_NUMERIC_CHECK);
//        }
//
//        return response()->json(['success'=>1,'data'=>$checkValidation], 200,[],JSON_NUMERIC_CHECK);

        $terminalId = $requestedData->terminalId;
        $terminalName = $requestedData->terminalName;
        $stockist_id = $requestedData->stockistId;
        $super_stockist_id = $requestedData->superStockistId;

        $terminal = User::findOrFail($terminalId);
        $terminal->user_name = $terminalName;
        $terminal->email = $requestedData->terminalName;
        $terminal->password = md5($requestedData->pin);
        $terminal->visible_password = $requestedData->pin;
        $terminal->pay_out_slab_id = $requestedData->payoutSlabId;
        $terminal->commission = $requestedData->commission;
        $terminal->save();

        $userRelation = UserRelationWithOther::whereTerminalId($terminalId)->whereActive(1)->first();
        if($stockist_id != ($userRelation->stockist_id)){
            $userRelation->changed_for = $terminalId;
            $userRelation->changed_by = $requestedData->userId;
            $userRelation->end_date = Carbon::today();
            $userRelation->active = 0;
            $userRelation->save();

            $checkUser = UserRelationWithOther::whereSuperStockistId($userRelation->super_stockist_id)->whereStockistId($userRelation->stockist_id)->whereActive(1)->first();
            if(!$checkUser){
                $userRelationCreate = new UserRelationWithOther();
                $userRelationCreate->super_stockist_id = $userRelation->super_stockist_id;
                $userRelationCreate->stockist_id = $userRelation->stockist_id;
                $userRelationCreate->save();
            }

//            return response()->json(['success'=>1,'data'=> $userRelation], 200,[],JSON_NUMERIC_CHECK);

            $checkValidation = UserRelationWithOther::whereSuperStockistId($userRelation->super_stockist_id)->whereStockistId($stockist_id)->whereTerminalId(null)->first();
//            return response()->json(['success'=>1,'data'=> $checkValidation], 200,[],JSON_NUMERIC_CHECK);

            if(!$checkValidation){
                $userRelationNull = new UserRelationWithOther();
                $userRelationNull->super_stockist_id = $userRelation->super_stockist_id;
                $userRelationNull->stockist_id = $userRelation->stockist_id;
                $userRelationNull->save();
            }

            $userRelationCreate = new UserRelationWithOther();
            $userRelationCreate->super_stockist_id = $super_stockist_id;
            $userRelationCreate->stockist_id = $stockist_id;
            $userRelationCreate->terminal_id = $terminalId;
            $userRelationCreate->save();
        }
//
//        $checkUser = UserRelationWithOther::whereSuperStockistId($super_stockist_id)->whereStockistId($stockist_id)->whereActive(1)->first();
//
//        if(!$checkUser){
//            $userRelationCreate = new UserRelationWithOther();
//            $userRelationCreate->super_stockist_id = $super_stockist_id;
//            $userRelationCreate->stockist_id = $stockist_id;
//            $userRelationCreate->save();
//        }


        return response()->json(['success'=>1,'data'=> new TerminalResource($terminal)], 200,[],JSON_NUMERIC_CHECK);
    }

    public function update_balance_to_terminal(Request $request){
        $requestedData = (object)$request->json()->all();

    // Validation for terminal
       $rules = array(
           'beneficiaryUid'=> ['required',
               function($attribute, $value, $fail){
                   $terminal=User::where('id', $value)->where('user_type_id','=',5)->first();
                   if(!$terminal){
                       return $fail($value.' is not a valid terminal id');
                   }
               }],
       );
       $messages = array(
           'beneficiaryUid.required' => "Terminal required"
       );

       $validator = Validator::make($request->all(),$rules,$messages);
       if ($validator->fails()) {
        return response()->json(['success'=>0, 'data' => $messages], 500);
    }

        DB::beginTransaction();
        try{

            $beneficiaryUid = $requestedData->beneficiaryUid;
            $amount = $requestedData->amount;
            $stockistId = $requestedData->stockistId;

            $beneficiaryObj = User::find($beneficiaryUid);
            $old_amount = $beneficiaryObj->closing_balance;
            $beneficiaryObj->closing_balance = $beneficiaryObj->closing_balance + $amount;
            $beneficiaryObj->save();
            $new_amount = $beneficiaryObj->closing_balance;

            $stockist = User::findOrFail($stockistId);
            $stockist->closing_balance = $stockist->closing_balance - $amount;
            $stockist->save();

            $rechargeToUser = new RechargeToUser();
            $rechargeToUser->beneficiary_uid = $requestedData->beneficiaryUid;
            $rechargeToUser->recharge_done_by_uid = $requestedData->rechargeDoneByUid;
            $rechargeToUser->old_amount = $old_amount;
            $rechargeToUser->amount = $requestedData->amount;
            $rechargeToUser->new_amount = $new_amount;
            $rechargeToUser->save();

            $transaction = new Transaction();
            $transaction->description = 'Recharged';
            $transaction->terminal_id = $requestedData->beneficiaryUid;
            $transaction->old_amount = $old_amount;
            $transaction->recharged_amount = $requestedData->amount;
            $transaction->new_amount = $new_amount;
            $transaction->save();

            DB::commit();

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['success'=>0, 'data' => null, 'error'=>$e->getMessage()], 500);
        }
        return response()->json(['success'=>1,'data'=> new TerminalResource($beneficiaryObj)], 200,[],JSON_NUMERIC_CHECK);

    }

    public function reset_terminal_password(Request $request){
        $requestedData = (object)$request->json()->all();

        $user = User::find($requestedData->userId);
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['success'=>0, 'message'=>'Wrong old password'], 200,[],JSON_NUMERIC_CHECK);
        }

        $user->password = md5($requestedData->newPassword);
        $user->visible_password = $requestedData->newPassword;
        $user->update();

        return response()->json(['success'=>1, 'message'=>'Password Updated'], 200,[],JSON_NUMERIC_CHECK);

    }

    public function reset_terminal_password_developer(Request $request){
        $requestedData = (object)$request->json()->all();

        $user = User::find($requestedData->userId);
        $user->password = md5($requestedData->newPassword);
        $user->visible_password = $requestedData->newPassword;
        $user->update();

        return response()->json(['success'=>1, 'message'=>'Password Updated'], 200,[],JSON_NUMERIC_CHECK);

    }



}
