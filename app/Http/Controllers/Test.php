<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\DrawMasterResource;
use App\Models\DrawMaster;
use App\Models\Game;
use App\Models\GameType;
use App\Models\NumberCombination;
use App\Models\PlayDetails;
use App\Models\PlayMaster;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use Litespeed\LSCache\LSCache;
use Litespeed\LSCache\LSCacheMiddleware;
use PhpParser\Node\Expr\Cast\Object_;
use Psy\Util\Json;

class Test extends Controller
{
    public function index()
    {
        $result = DrawMaster::whereDoesnthave('result_masters', function($q){
            $q->where('game_date', '=', '2021-05-24');
        })->get();
        return response()->json(['success'=>1,'data'=>$result], 200,[],JSON_NUMERIC_CHECK);
    }

    public function testNew($id){

        $data = DB::select("select ifnull(sum(table1.amount),0) as amount from (select  play_details.combined_number, if(play_details.combined_number>1, play_details.quantity * 1, play_details.quantity * game_types.mrp ) as amount from play_masters
                    inner join play_details on play_masters.id = play_details.play_master_id
                    inner join game_types on play_details.game_type_id = game_types.id
                    where play_masters.id = ? and play_details.series_id = 0) as table1;",[$id])[0]->amount;

        $data2 = DB::select("select ifnull(sum(table1.amount),0) as amount from (select distinct  play_details.combined_number, if(play_details.combined_number>1, play_details.quantity * 1, play_details.quantity * game_types.mrp ) as amount from play_masters
                    inner join play_details on play_masters.id = play_details.play_master_id
                    inner join game_types on play_details.game_type_id = game_types.id
                    where play_masters.id = ? and play_details.series_id <> 0) as table1;",[$id])[0]->amount;

        return response()->json(['success'=>$data,'data'=>$data2], 200,[],JSON_NUMERIC_CHECK);

//        $gameTest = Game::find(1)->game_types;
//        return response()->json(['success'=>1,'data'=>$gameTest], 200,[],JSON_NUMERIC_CHECK);
//        $x = Cache::remember('testCache', 3000000, function (){
//            return 2.1;
//        });
//        $draw_master = DrawMaster::whereActive(1)->whereGameId(1)->first();
//        $min_draw = Carbon::parse($draw_master->end_time)->minute;
//        $day_draw = Carbon::parse($draw_master->end_time)->day;
//        $hour_draw = Carbon::parse($draw_master->end_time)->hour;
//        $min_now = Carbon::now()->minute ;
//        $day_now = Carbon::now()->day ;
//        $hour_now = Carbon::now()->hour ;
//        if(($day_draw === $day_now) && ($min_draw<=$min_now) && ($hour_draw==$hour_now) && (($min_now % $draw_master->time_diff) != 0)){
//            return response()->json([
//                    'success'=>1
//                    ,'$day_draw'=>$day_draw
//                    ,'$day_now'=>$day_now
//                    ,'$min_draw'=>$min_draw
//                    ,'$min_now'=>$min_now
//                    ,'$hour_draw'=>$hour_draw
//                    ,'$hour_now'=>$hour_now
//                    ,'$draw_master->time_diff 00'=>$draw_master->time_diff,
//                    'status' => "trigger"
//                ]
//                , 200,[],JSON_NUMERIC_CHECK);
//        }
//        return response()->json([
//            'success'=>0
//                ,'$day_draw'=>$day_draw
//                ,'$day_now'=>$day_now
//                ,'$min_draw'=>$min_draw
//                ,'$min_now'=>$min_now
//                ,'$hour_draw'=>$hour_draw
//                ,'$hour_now'=>$hour_now
//                ,'$draw_master->time_diff 00'=>$draw_master->time_diff
//            ]
//            , 200,[],JSON_NUMERIC_CHECK);
//        $play_master = PlayMaster::where( 'created_at', '>', Carbon::now()->subDays(2)->format('Y-m-d'))->get();

//        $activeUsers = PersonalAccessToken::whereTokenableId(collect($terminals))->get();

//        return response()->json(['success'=>Carbon::now()->subDays(2),'data'=>$play_master], 200,[],JSON_NUMERIC_CHECK);
//        return User::get();

//        $x = Cache::get('testCache');
//        if($x){
//           if($x < $id){
//               Cache::forget('testCache');
//               $x = Cache::remember('testCache', 3000000, function () use ($id) {
//                   return $id;
//               });
//           }
//            return response()->json(['success'=>1,'data'=>$x], 200,[],JSON_NUMERIC_CHECK);
//        }else{
//            $x = Cache::remember('testCache', 3000000, function () use ($id) {
//                return $id;
//            });
//            return response()->json(['success'=>11,'data'=>$x], 200,[],JSON_NUMERIC_CHECK);
//        }
//        return $x;

//        $x = Carbon::now()->subDays(30);
//
//        DB::select("delete from play_masters where date(created_at) = ".$x);

//        $transaction = new Transaction();
//        $transaction->terminal_id = 12;
//        $transaction->play_master_id = 4;
//        $transaction->old_amount = 200;
//        $transaction->prize_amount = 10;
//        $transaction->new_amount = 500;
//        $transaction->save();

//        $transaction = DB::insert("insert into transactions (
//              terminal_id
//              ,play_master_id
//              ,old_amount
//              ,prize_amount
//              ,new_amount
//            ) VALUES (
//              ? -- terminal_id - IN bigint unsigned
//              ,? -- play_master_id - IN int
//              ,? -- old_amount - IN decimal(50,2)
//              ,? -- prize_amount - IN decimal(50,2)
//              ,? -- new_amount - IN decimal(50,2)
//            )", array(12,4,200,10,500));
//
//        return response()->json(['success'=>1,'data'=>$transaction], 200,[],JSON_NUMERIC_CHECK);

//        $test = DB::select("select * from play_masters
//            where date(created_at) = '2022-09-14'
//            order by id desc
//            limit 1")[0];
//
//        $date = Carbon::parse($test->created_at)->format('Y-m-d');
//        $datework = Carbon::createFromDate($date);
//        $now = Carbon::now();
//        $testdate = $datework->diffInDays($now);
//
//        return $testdate;

//        Cache::get('allTerminal');

//        $value = Cache::remember('users', 100, function () {
//            return Game::get();
//    });

//        $newa = DB::select("select game_type_id from ?",[collect($value)->all()]);


//        return collect($value)->where('game_type_id', 1)->all();
//        return Object.entries(obj) collect($value)->where('game_type_id', 1)->all();
//        return json_decode(json_encode(collect($value)->where('game_type_id', 1)->all()), true)->to;

//        return $newa;

//        $set_game_date = Carbon::today()->addDays(1)->format('Y-m-d');
//         if((Carbon::today()->format('Y-m-d')) === Carbon::today()->addDays(1)->format('Y-m-d')){
//             $test = true;
//        }else{
//             $test = false;
//         }
//        return $test;

//        $today= Carbon::today()->format('Y-m-d');
//        $nPlay = PlayMaster::whereDrawMasterId(6)
//            ->whereDate('created_at',$today)
//            ->get();
//        return response()->json(['success'=>1, 'test1' => $nPlay], 200,[],JSON_NUMERIC_CHECK);

        //clear cache
//        LSCache::purgeAll();

        //get referer
//        return request()->headers->get('referer');
//        $current_time = Carbon::now();

        //get server ip address
//        $localIp = gethostbyname(gethostname());
//        return request()->server('SERVER_ADDR');
    }

}
