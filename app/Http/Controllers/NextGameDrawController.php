<?php

namespace App\Http\Controllers;

use App\Models\NextGameDraw;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NextGameDrawController extends Controller
{
    public function index(){
        $data = NextGameDraw::first();
        return $data;
    }

    public function getNextDrawIdOnly(){
//        $nextGameDrawObj = NextGameDraw::first();
//        $result['id'] = $nextGameDrawObj->next_draw_id;
        $nextGameDrawObj = NextGameDraw::select(DB::raw('next_draw_id as id'), 'game_id')->get();
        return response()->json(['success'=> 1, 'data' => $nextGameDrawObj], 200);
    }
}
