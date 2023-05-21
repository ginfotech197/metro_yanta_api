<?php

namespace App\Console;

use App\Console\Commands\GenerateResult;
use App\Http\Controllers\CentralController;
use App\Http\Controllers\CommonFunctionController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        '\App\Console\Commands\GenerateResult',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        //$schedule->command('generate:result')->everyThirtyMinutes()->timezone('Asia/Kolkata');

//        $schedule->command('generate:result')->cron('50 10-13,15-19 * * * ')->timezone('Asia/Kolkata');
//        $schedule->command('generate:result')->dailyAt('21:00')->timezone('Asia/Kolkata');


//        $schedule->command('generateFatafat:result')->cron('30 11,14,17,20 * * * ')->timezone('Asia/Kolkata');
//        $schedule->command('generateFatafat:result')->cron('00 10,13,16,19 * * * ')->timezone('Asia/Kolkata');
//        $schedule->command('generateShirdi:result')->cron('30 10,13,16,18,19 * * * ')->timezone('Asia/Kolkata');
//        $schedule->command('generateShirdi:result')->cron('00 12,15,21 * * * ')->timezone('Asia/Kolkata');
//        $schedule->command('drawOver:update')->dailyAt('00:00')->timezone('Asia/Kolkata');


//        LOG::info('-------------------------------------------------------------------------------------------------------------------------');

        $schedule->command('drawOver:update')->dailyAt('00:00')->timezone('Asia/Kolkata');
        $schedule->command('clear:tokens')->dailyAt('00:00')->timezone('Asia/Kolkata');

//        $schedule->command('generateTripleChance:result')->cron('*/2 * * * *')->timezone('Asia/Kolkata');

        $schedule->command('generate:result')->cron('* * * * *')->timezone('Asia/Kolkata');

        //1 min 30 sec
//        $schedule->command('generateRollet:result')->cron('* * * * *')->timezone('Asia/Kolkata');
//        $schedule->command('generateRollet:result')->everyMinute();

        //sleep(0)
        $schedule->command('generateDouble:result')->cron('*/3 * * * *')->timezone('Asia/Kolkata');

        //sleep(54);
        $schedule->command('generateTwelveCard:result')->cron('1-59/2 * * * *')->timezone('Asia/Kolkata');
        $schedule->command('generateSingle:result')->cron('1-59/2 * * * *')->timezone('Asia/Kolkata');
        $schedule->command('generateSixteenCard:result')->cron('1-59/2 * * * *')->timezone('Asia/Kolkata');
        $schedule->call(function () {
            $centralController = new CentralController();
            $centralController->createResult(6,1);
        })->cron('1-59/2 * * * *')->timezone('Asia/Kolkata');

        $schedule->command('generateTripleChance:result')->cron('1-59/2 * * * *')->timezone('Asia/Kolkata');

        //database Backup
//        $schedule->call(function () {
//            $commonFunctionController = new CommonFunctionController();
//            $commonFunctionController->backup_database();
//        })->weekly()->mondays()->at('02:00');

        //keep 42 days data only 2 days for security
        $schedule->call(function () {
            $centralController = new CentralController();
            $centralController->delete_data_except_thirty_days();
        })->dailyAt('03:00')->timezone('Asia/Kolkata');

        //reset everyday approve
        $schedule->call(function () {
            $centralController = new CentralController();
            $centralController->reset_approve_everyday();
        })->dailyAt('00:00')->timezone('Asia/Kolkata');

        //cache files
//       $schedule->command('config:cache')->dailyAt('00:00')->timezone('Asia/Kolkata');
//       $schedule->command('route:cache')->dailyAt('00:00')->timezone('Asia/Kolkata');

        // $schedule->command('config:cache')->everyThreeHours()->timezone('Asia/Kolkata');
        // $schedule->command('route:cache')->everyThreeHours()->timezone('Asia/Kolkata');

//        LOG::info('-------------------------------------------------------------------------------------------------------------------------');


//        $schedule->command('clear:data')->dailyAt('00:00')->timezone('Asia/Kolkata');

//        $schedule->command('sanctum:prune-expired --hours=24')->daily();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
