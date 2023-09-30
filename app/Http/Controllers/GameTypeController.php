<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameTypeResource;
use App\Models\Game;
use App\Models\GameType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameTypeController extends Controller
{
    public function index()
    {
        $result = GameType::get();
//        $result = get_age('1977-05-20');
        // return response()->json(['success'=>1,'data'=> $result], 200,[],JSON_NUMERIC_CHECK);
        return response()->json(['success'=>1,'data'=> GameTypeResource::collection($result)], 200,[],JSON_NUMERIC_CHECK);
    }

    public function update_multiplexer(Request $request){
        $requestedData = (object)($request->json()->all());
        $game = Game::find((GameType::find($requestedData->gameTypeId))->game_id);
        $game->multiplexer_random = $requestedData->multiplexer;
        $game->active = $requestedData->active;
        $game->update();

        return response()->json(['success'=>1,'data'=> GameTypeResource::collection(GameType::get())], 200,[],JSON_NUMERIC_CHECK);
    }

    public function update_payout(Request $request){
        $requestedData = $request->json()->all();
        $inputPayoutDetail = $requestedData[0];

        $gameType = GameType::find($inputPayoutDetail['gameTypeId']);
        $gameType->payout = $inputPayoutDetail['newPayout'];
        $gameType->multiplexer = $inputPayoutDetail['multiplexer'];
        $gameType->counter = $inputPayoutDetail['counter'];
        $gameType->save();

        return response()->json(['success'=>1,'data'=> $inputPayoutDetail], 200,[],JSON_NUMERIC_CHECK);

        if(count($inputPayoutDetails)>1){
//            return response()->json(['success'=>1,'data'=> $inputPayoutDetails], 200,[],JSON_NUMERIC_CHECK);
            foreach ($inputPayoutDetails as $inputPayoutDetail){
                $gameType = GameType::find($inputPayoutDetail['gameTypeId']);
                $gameType->payout = $inputPayoutDetail['newPayout'];
                $gameType->multiplexer = $inputPayoutDetail['multiplexer'];
                $gameType->counter = $inputPayoutDetail['counter'];
                $gameType->save();

                if($inputPayoutDetail['gameTypeId'] == 1){
                    $gameType = GameType::find(2);
                    $gameType->payout = $inputPayoutDetail['newPayout'];
                    $gameType->multiplexer = $inputPayoutDetail['multiplexer'];
                    $gameType->counter = $inputPayoutDetail['counter'];
                    $gameType->save();

                    $gameType = GameType::find(5);
                    $gameType->payout = $inputPayoutDetail['newPayout'];
                    $gameType->multiplexer = $inputPayoutDetail['multiplexer'];
                    $gameType->counter = $inputPayoutDetail['counter'];
                    $gameType->save();
                }

                if($inputPayoutDetail['gameTypeId'] == 7){
                    $gameType = GameType::find(8);
                    $gameType->payout = $inputPayoutDetail['newPayout'];
                    $gameType->multiplexer = $inputPayoutDetail['multiplexer'];
                    $gameType->counter = $inputPayoutDetail['counter'];
                    $gameType->save();

                    $gameType = GameType::find(9);
                    $gameType->payout = $inputPayoutDetail['newPayout'];
                    $gameType->multiplexer = $inputPayoutDetail['multiplexer'];
                    $gameType->counter = $inputPayoutDetail['counter'];
                    $gameType->save();
                }
            }

            $getAllGameType = GameType::get();
            return response()->json(['success'=>1,'data'=> GameTypeResource::collection($getAllGameType)], 200,[],JSON_NUMERIC_CHECK);

//            return response()->json(['success'=>1,'data'=> count($inputPayoutDetails)], 200,[],JSON_NUMERIC_CHECK);
        }


//        return response()->json(['success'=>1,'data'=> count($inputPayoutDetails)], 200,[],JSON_NUMERIC_CHECK);

//        $idGameType = [1,2,5];

//        return response()->json(['success'=>$inputPayoutDetails[0], 'data' => ($inputPayoutDetails[0])['gameTypeId']], 200);

//        if(($inputPayoutDetails[0])['gameTypeId'] == 1){
//            $idGameType = [1,2,5];
//            for($i=0; $i<3; $i++){
//                $gameType = GameType::find($idGameType[$i]);
//                $gameType->payout = ($inputPayoutDetails[0])['newPayout'];
//                $gameType->multiplexer = ($inputPayoutDetails[0])['multiplexer'];
//                $gameType->save();
//            }
//            $getAllGameType = GameType::get();
//            return response()->json(['success'=>1,'data'=> GameTypeResource::collection($getAllGameType)], 200,[],JSON_NUMERIC_CHECK);
//        }else{
//            $detail = (object)$inputPayoutDetails;
//            for($i=0; $i<3; $i++){


            if(($inputPayoutDetails[0])['gameTypeId'] === 1){
                $gameType = GameType::find(1);
                $gameType->payout = ($inputPayoutDetails[0])['newPayout'];
                $gameType->multiplexer = ($inputPayoutDetails[0])['multiplexer'];
                $gameType->save();

                $gameType =  GameType::find(2);
                $gameType->payout = ($inputPayoutDetails[0])['newPayout'];
                $gameType->multiplexer = ($inputPayoutDetails[0])['multiplexer'];
                $gameType->save();

                $gameType =  GameType::find(5);
                $gameType->payout = ($inputPayoutDetails[0])['newPayout'];
                $gameType->multiplexer = ($inputPayoutDetails[0])['multiplexer'];
                $gameType->save();

            }else if(($inputPayoutDetails[0])['gameTypeId'] === 7){

                $gameType = GameType::find(7);
                $gameType->payout = ($inputPayoutDetails[0])['newPayout'];
                $gameType->multiplexer = ($inputPayoutDetails[0])['multiplexer'];
                $gameType->save();

                $gameType =  GameType::find(8);
                $gameType->payout = ($inputPayoutDetails[0])['newPayout'];
                $gameType->multiplexer = ($inputPayoutDetails[0])['multiplexer'];
                $gameType->save();

                $gameType =  GameType::find(9);
                $gameType->payout = ($inputPayoutDetails[0])['newPayout'];
                $gameType->multiplexer = ($inputPayoutDetails[0])['multiplexer'];
                $gameType->save();

            }
            else{
                $gameType = GameType::find(($inputPayoutDetails[0])['gameTypeId']);
                $gameType->payout = ($inputPayoutDetails[0])['newPayout'];
                $gameType->multiplexer = ($inputPayoutDetails[0])['multiplexer'];
                $gameType->save();
            }

//            }
            $getAllGameType = GameType::get();
            return response()->json(['success'=>1,'data'=> GameTypeResource::collection($getAllGameType)], 200,[],JSON_NUMERIC_CHECK);

//        }

//        DB::beginTransaction();
//        try{
//            $outputPayoutDetails = array();
//            foreach($inputPayoutDetails as $inputPayoutDetail){
//                $detail = (object)$inputPayoutDetail;
//                $gameType = GameType::find($detail->gameTypeId);
//                $gameType->payout = $detail->newPayout;
//                $gameType->save();
//                $outputPayoutDetails[] = $gameType;
//            }
//            DB::commit();
//        }catch(\Exception $e){
//            DB::rollBack();
//            return response()->json(['success'=>0, 'data' => null, 'error'=>$e->getMessage()], 500);
//        }

//        $getAllGameType = GameType::get();
//        return response()->json(['success'=>1,'data'=> GameTypeResource::collection($getAllGameType)], 200,[],JSON_NUMERIC_CHECK);
    }


}
