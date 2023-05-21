<?php

namespace App\Http\Controllers;

use App\Http\Resources\RechargeToUserResource;
use App\Models\RechargeToUser;
use App\Models\User;
use Illuminate\Http\Request;

class RechargeToUserController extends Controller
{
    public function getTransactionByUserForAdmin(Request $request){
        $requestedData = (object)$request->json()->all();

//        $user = User::find($requestedData->rechargedByID);

        $data = RechargeToUser::select()
            ->whereBeneficiaryUid($requestedData->rechargedToID)
            ->orderBy('created_at', 'desc')
            ->get();

//        if($user->user_type_id === 1){
//            $data = RechargeToUser::select()
//                ->whereBeneficiaryUid($requestedData->rechargedToID)
//                ->orderBy('created_at', 'desc')
//                ->get();
//        }else{
//            $data = RechargeToUser::select()
//                ->whereRechargeDoneByUid($requestedData->rechargedByID)
//                ->whereBeneficiaryUid($requestedData->rechargedToID)
//                ->orderBy('created_at', 'desc')
//                ->get();
//        }


        return response()->json(['success'=>1,'data'=> RechargeToUserResource::collection($data)], 200);
    }

    public function getTransactionByUser(Request $request){
        $requestedData = (object)$request->json()->all();

        $data = RechargeToUser::select()
            ->whereBeneficiaryUid($requestedData->rechargedToID)
            ->whereRechargeDoneByUid($requestedData->rechargedByID)
            ->orderBy('created_at', 'desc')
            ->get();


        return response()->json(['success'=>1,'data'=> RechargeToUserResource::collection($data)], 200);
    }

}
