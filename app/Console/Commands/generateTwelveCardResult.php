<?php

namespace App\Console\Commands;

use App\Http\Controllers\CentralController;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class generateTwelveCardResult extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generateTwelveCard:result';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        // LOG::info('Twelve card: '.Carbon::today());
        Sleep(54);
        $centralControllerObj = new CentralController();
        $ret = $centralControllerObj->createResult(2,1);
        // LOG::info('Twelve card: '.$ret);
    }
}
