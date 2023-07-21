<?php

namespace App\Http\Controllers;

use App\Http\Resources\StockistResource;
use App\Http\Resources\SuperStockistResource;
use App\Http\Resources\TerminalResource;
use App\Http\Resources\UserResource;
use App\Models\PlayMaster;
use App\Models\User;
use App\Models\UserRelationWithOther;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{

    public function register(Request $request)
    {

        $user = User::create([
            'email'    => $request->email,
            'password' => $request->password,
            'user_name' => $request->name,
            'user_type_id' => $request->user_type_id
        ]);

//        return response()->json(['success'=>1,'data'=>$user], 200,[],JSON_NUMERIC_CHECK);

        $token = $user->createToken('my-app-token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    function login(Request $request)
    {
        if(empty($request->json()->all())){
            return response()->json(['success'=>4,'data'=>null, 'message'=>'Array is null'], 200,[],JSON_NUMERIC_CHECK);
        }

        if(!($request->devToken)){
            return response()->json(['success'=>3,'data'=>null, 'message'=>'Dev token not found'], 200,[],JSON_NUMERIC_CHECK);
        }

        $user= User::where('email', $request->email)->first();

        if($user){
            if($user->blocked == 1){
                return response()->json(['success'=>2,'data'=>null, 'message'=>'You are blocked'], 200,[],JSON_NUMERIC_CHECK);
            }
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['success'=>0,'data'=>null, 'message'=>'Credential does not matched'], 200,[],JSON_NUMERIC_CHECK);
        }

        if($request->devToken == 'webTokenAccess'){

            $token = $user->createToken('my-app-token')->plainTextToken;

            $response = [
                'user' => new UserResource($user),
                'token' => $token
            ];
            return response()->json(['success'=>1,'data'=>$response, 'message'=>'Welcome'], 200,[],JSON_NUMERIC_CHECK);

        }else if(($request->devToken == 'unityAccessToken') && ($user->user_type_id == 5)){

            if(!($request->ver)){
                return response()->json(['success'=>0,'data'=>null, 'message'=>'Contact admin ver not found'], 200,[],JSON_NUMERIC_CHECK);
            }

            $user->platform = $request->platform;
            if($request->ver == 'NP'){
                $user->auto_claim = 1;
                $user->version = 'NP';
            }else{
                $user->version = 'P';
            }
            $user->save();

            $appVer = $request->appVer;
            $cacheAppVer = Cache::get('cacheAppVersion');
            if(($cacheAppVer)){
                if($cacheAppVer <= $appVer){
                    Cache::forget('cacheAppVersion');
                    $cacheAppVer = Cache::forever('cacheAppVersion', $appVer);
                }
            }else{
                $cacheAppVer = Cache::forever('cacheAppVersion', $appVer);
            }

            if(!($request->appVer == $cacheAppVer)){
                return response()->json(['success'=>0,'data'=>null, 'message'=>'Update app to login'], 200,[],JSON_NUMERIC_CHECK);
            }

            if(($user->login_activate == 1)){
                return response()->json(['success'=>0,'data'=>null, 'message'=>'Pending Approval'], 200,[],JSON_NUMERIC_CHECK);
            }

            if($user->login_activate == 0){
                $user->temp_mac_address = $request->mac_address;
                $user->mac_address = $request->mac_address;
                $user->login_activate = 2;
                $user->save();

                return response()->json(['success'=>0,'data'=>null, 'message'=>'Needs Approval'], 200,[],JSON_NUMERIC_CHECK);
            }

            if($user->mac_address != $request->mac_address){
                $user->temp_mac_address = $request->mac_address;
                $user->login_activate = 1;
                $user->save();

                return response()->json(['success'=>0,'data'=>null, 'message'=>'Needs approval mac mismatch'], 200,[],JSON_NUMERIC_CHECK);
            }

//            $personalAccessToken = PersonalAccessToken::whereTokenableId($user->id)->get();
//            foreach ($personalAccessToken as $x){
//                $x->delete();
//            }

            DB::select("delete from personal_access_tokens where tokenable_id = ".$user->id);

            $token = $user->createToken('my-app-token')->plainTextToken;

            $response = [
                'user' => new UserResource($user),
                'token' => $token
            ];

            return response()->json(['success'=>1,'data'=>$response, 'message'=>'Welcome'], 200,[],JSON_NUMERIC_CHECK);

        }else{
            return response()->json(['success'=>0,'data'=>null, 'message'=>'Data Error'], 200,[],JSON_NUMERIC_CHECK);
        }

    }

    public function delete_personal_access_tokens(){
        // Artisan::call('optimize:clear');
//        $personalAccessToken = PersonalAccessToken::get();
//        foreach ($personalAccessToken as $x){
//            $x->delete();
//        }

        DB::select("truncate personal_access_tokens;");
    }


    function getCurrentUser(Request $request){
        return $request->user();
    }

    function getAllUsers(Request $request){
        return User::get();
    }

    function logout(Request $request){
        $result = $request->user()->currentAccessToken()->delete();
        return $result;
    }


//    function logout($id){
//        DB::table('personal_access_tokens')->where('tokenable_id', $id)->delete();
//        return $id;
//    }

    function  update(Request $request){

        DB::beginTransaction();
        try{
            $requestedData = (object)$request->json()->all();
            $user = User::findOrFail($requestedData->userId);
            $user->closing_balance-= $requestedData->deductAmount;
            $user->save();

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json(['success'=>0, 'data' => null, 'error'=>$e->getMessage()], 500);
        }
        return response()->json(['success'=>1,'data'=> new UserResource($user)], 200,[],JSON_NUMERIC_CHECK);
    }

    public function check_pin(Request $request){
        $requestedData = (object)($request->json()->all());
//        return response()->json(['success'=>$request, 'test' =>(object)$requestedData], 200);
        $userPinValidation = User::whereEmail($requestedData->email)->first();
        if($userPinValidation){
            return response()->json(['success'=>0], 200,[],JSON_NUMERIC_CHECK);
        }
        return response()->json(['success'=>1], 200,[],JSON_NUMERIC_CHECK);
    }

    public function update_block(Request $request){
        $requestedData = (object)($request->json()->all());
        $user = User::find($requestedData->id);

        if($user->user_type_id == 5){

                $user = User::find($requestedData->id);
                $user->blocked = ($user->blocked == 1) ? 0 : 1;
                $user->save();
                return response()->json(['success' => 1, 'data' =>new TerminalResource($user), 'message' => 'Terminal Updated'], 200,[],JSON_NUMERIC_CHECK);

        }

        if($user->user_type_id == 4){
                $user = User::find($requestedData->id);
                $user->blocked = ($user->blocked == 1) ? 0 : 1;
                $user->save();

                if($user->blocked == 1){

                    $allTerminalIds = UserRelationWithOther::whereStockistId($user->id)->whereActive(1)->get();
                    if( count($allTerminalIds)>0 ){
                        foreach ($allTerminalIds as $allTerminalId){
                            $user1 = User::find($allTerminalId->terminal_id);
                            if($user1->blocked == 0){
                                $user1->blocked = 1;
                                $user1->save();
                            }
                        }
                    }

                }
                return response()->json(['success' => 1, 'data' =>new StockistResource($user), 'message' => 'Stockist Updated'], 200,[],JSON_NUMERIC_CHECK);
//                return response()->json(['success' => 1, 'data' =>$user, 'message' => 'Stockist Updated'], 200,[],JSON_NUMERIC_CHECK);

        }

        if($user->user_type_id == 3){

            $user = User::find($requestedData->id);
            $user->blocked = ($user->blocked == 1) ? 0 : 1;
            $user->save();

            if($user->blocked == 1){

                $allStockistIds = UserRelationWithOther::whereSuperStockistId($user->id)->whereActive(1)->get();
                if(count($allStockistIds)>0){
                    foreach ($allStockistIds as $allStockistId){
                        $user1 = User::find($allStockistId->stockist_id);
                        if($user1->blocked == 0){
                            $user1->blocked = 1;
                            $user1->save();
                        }

                        $user2 = User::find($allStockistId->terminal_id);
                        if($user2->blocked == 0){
                            $user2->blocked = 1;
                            $user2->save();
                        }

                    }
                }

            }
            return response()->json(['success' => 1, 'data' =>new SuperStockistResource($user), 'message' => 'Super Stockist Updated'], 200,[],JSON_NUMERIC_CHECK);

        }


        return response()->json(['success' => 0, 'data' =>null], 200,[],JSON_NUMERIC_CHECK);
    }

}
