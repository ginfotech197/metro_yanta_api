<?php

namespace App\Console\Commands;

use App\Models\DrawMaster;
use App\Models\NextGameDraw;
use App\Models\ResultMaster;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\CentralController;

class GenerateResult extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:result';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create results everyday';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //triple chance
        $draw_master = DrawMaster::whereActive(1)->whereGameId(1)->first();
        $min_draw = Carbon::parse($draw_master->end_time)->minute;
        $day_draw = Carbon::parse($draw_master->end_time)->day;
        $hour_draw = Carbon::parse($draw_master->end_time)->hour;
        $min_now = Carbon::now()->minute ;
        $day_now = Carbon::now()->day ;
        $hour_now = Carbon::now()->hour ;
        if(($day_draw === $day_now) && ($min_draw<=$min_now) && ($hour_draw==$hour_now) && (($min_now % $draw_master->time_diff) != 0)){
            $centralControllerObj = new CentralController();
            $ret = $centralControllerObj->createResult(1,1);
        }

        //12 card
        $draw_master = DrawMaster::whereActive(1)->whereGameId(2)->first();
        $min_draw = Carbon::parse($draw_master->end_time)->minute;
        $day_draw = Carbon::parse($draw_master->end_time)->day;
        $hour_draw = Carbon::parse($draw_master->end_time)->hour;
        $min_now = Carbon::now()->minute ;
        $day_now = Carbon::now()->day ;
        $hour_now = Carbon::now()->hour ;
        if(($day_draw === $day_now) && ($min_draw<=$min_now) && ($hour_draw==$hour_now) && (($min_now % $draw_master->time_diff) != 0)){
            $centralControllerObj = new CentralController();
            $ret = $centralControllerObj->createResult(2,1);
        }

        //16 card
        $draw_master = DrawMaster::whereActive(1)->whereGameId(3)->first();
        $min_draw = Carbon::parse($draw_master->end_time)->minute;
        $day_draw = Carbon::parse($draw_master->end_time)->day;
        $hour_draw = Carbon::parse($draw_master->end_time)->hour;
        $min_now = Carbon::now()->minute ;
        $day_now = Carbon::now()->day ;
        $hour_now = Carbon::now()->hour ;
        if(($day_draw === $day_now) && ($min_draw<=$min_now) && ($hour_draw==$hour_now) && (($min_now % $draw_master->time_diff) != 0)){
            $centralControllerObj = new CentralController();
            $ret = $centralControllerObj->createResult(3,1);
        }

        //single
        $draw_master = DrawMaster::whereActive(1)->whereGameId(4)->first();
        $min_draw = Carbon::parse($draw_master->end_time)->minute;
        $day_draw = Carbon::parse($draw_master->end_time)->day;
        $hour_draw = Carbon::parse($draw_master->end_time)->hour;
        $min_now = Carbon::now()->minute ;
        $day_now = Carbon::now()->day ;
        $hour_now = Carbon::now()->hour ;
        if(($day_draw === $day_now) && ($min_draw<=$min_now) && ($hour_draw==$hour_now) && (($min_now % $draw_master->time_diff) != 0)){
            $centralControllerObj = new CentralController();
            $ret = $centralControllerObj->createResult(4,1);
        }

        //double chance
        $draw_master = DrawMaster::whereActive(1)->whereGameId(5)->first();
        $min_draw = Carbon::parse($draw_master->end_time)->minute;
        $day_draw = Carbon::parse($draw_master->end_time)->day;
        $hour_draw = Carbon::parse($draw_master->end_time)->hour;
        $min_now = Carbon::now()->minute;
        $day_now = Carbon::now()->day;
        $hour_now = Carbon::now()->hour;
        if(($day_draw === $day_now) && ($min_draw<=$min_now) && ($hour_draw==$hour_now) && (($min_now % $draw_master->time_diff) != 0)){
            $centralControllerObj = new CentralController();
            $ret = $centralControllerObj->createResult(5,1);
        }

        //rollet
        $draw_master = DrawMaster::whereActive(1)->whereGameId(6)->first();
        $min_draw = Carbon::parse($draw_master->end_time)->minute;
        $day_draw = Carbon::parse($draw_master->end_time)->day;
        $hour_draw = Carbon::parse($draw_master->end_time)->hour;
        $min_now = Carbon::now()->minute;
        $day_now = Carbon::now()->day;
        $hour_now = Carbon::now()->hour;
        if(($day_draw === $day_now) && ($min_draw<=$min_now) && ($hour_draw==$hour_now) && (($min_now % $draw_master->time_diff) != 0)){
            $centralControllerObj = new CentralController();
            $ret = $centralControllerObj->createResult(6,1);
        }

    }
}
