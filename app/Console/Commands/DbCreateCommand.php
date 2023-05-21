<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Vkovic\LaravelCommando\Handlers\Database\WithDbHandler;

class DbCreateCommand extends Command
{
    use WithDbHandler;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create  {database? : Database to create. If omitted, name from .env will be used.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database';

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
            $this->output->warning("Database `$database` exists");

            return 1;
        }

        $this->dbHandler()->createDatabase($database);

        $this->output->success("Database `$database` created successfully");
    }
}
