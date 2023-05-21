<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Vkovic\LaravelCommando\Handlers\Database\WithDbHandler;

class DbExistCommand extends Command
{
    use WithDbHandler;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:exist
                                {database? : Database name to check. If omitted it`ll check for default db (defined in .env).}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if database exists';

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
        $database = $this->argument('database')
            ?: config('database.connections.' . config('database.default') . '.database');

        if ($this->dbHandler()->databaseExists($database)) {
            $this->output->note("Database `$database` exists");
        } else {
            $this->output->note("Database `$database` doesn`t exist");
        }
        $this->output->warning("This structure created by CODER, Contact Sukanta Hui, sukantahui@gmail.com");
    }
}
