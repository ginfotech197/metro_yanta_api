<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Vkovic\LaravelCommando\Handlers\Database\WithDbHandler;
use Vkovic\LaravelCommando\Handlers\WithHelper;

class DbSummonCommand extends Command
{
    use WithDbHandler, WithHelper;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:bouma';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop default database, than perform migrate followed with the seed';

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
        // TODO
        // Prevent on production
        // or make it run only if `--force` flag is passed

        $database = config('database.connections.' . config('database.default') . '.database');

        $this->output->warning('This command will:');
        $this->output->listing([
            "drop database `$database` if one exists",
            "create empty database `$database`",
            "migrate database (same as `php artisan migrate`)",
            "seed database (same as `php artisan db:seed`)",
        ]);

        if (!$this->confirm('Do you really wish to continue?')) {
            $this->output->note('Command aborted');

            return 255;
        }

        if ($this->dbHandler()->databaseExists($database)) {
            $this->dbHandler()->dropDatabase($database);
        }

        $this->dbHandler()->createDatabase($database);

        $this->helper()->artisanCall('migrate');
        $this->helper()->artisanCall('db:seed');

        $this->output->success("Database `$database` summoned successfully, Created by CODER, contact Sukanta Hui, +917003756860, sukantahui@gmail.com");
    }
}
