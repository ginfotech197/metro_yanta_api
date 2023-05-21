<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class CommonFunctionController extends Controller
{
    public function upload_installer(Request $request){
        $file = $request->file('file')->move(storage_path("/installer"), $request['fileName']);
//        $file = $request->file('file')->move(storage_path("/installer"), 'x.zip');
        return response()->json(['success'=>$request['fileName']], 200);
    }

    public function getServerTime(){

//        return request()->headers->get('referer');
//        $current_time = Carbon::now();

        $current_time = Carbon::now();
        return array('hour' => $current_time->hour, 'minute' => $current_time->minute,
            'second' => $current_time->second, 'meridiem' => $current_time->meridiem);
    }

    public function backup_database()
    {
        \Artisan::call('db:dump');
        $result = Artisan::output();
        $replaced = Str::substr($result,6);
        $replaced = Str::replaceLast('\\r\\n', '\r\n', $replaced);
        return response()->json(['success'=>1, 'data' => $replaced], 200);

    }

}
