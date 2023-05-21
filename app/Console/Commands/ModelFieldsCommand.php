<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Vkovic\LaravelCommando\Handlers\Database\WithDbHandler;
use Vkovic\LaravelCommando\Handlers\WithHelper;

class ModelFieldsCommand extends Command
{
    use WithHelper, WithDbHandler;
    /**
     * The name and signature of the console command.
     *
     * @var string
    */
    protected $signature = 'model:fields
                                {model? : Model to show fields from (e.g. `"App\User"`). If omitted, list of all models will be shown to choose from.)}
                           ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show model fields info';

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
        try {
            $modelClass = $this->getModelClass();
        } catch (\Exception $e) {
            $this->output->warning($e->getMessage());

            return 1;
        }

        $tableFields = ['Field', 'Type', 'Nullable', 'Default', 'Casts', 'Guarded', 'Fillable'];
        $tableData = $this->tableData($modelClass);

        $this->output->text("Model: `$modelClass`");
        $this->table($tableFields, $tableData);
        $this->output->newLine();
    }

    public function getModelClass()
    {
        $modelClass = $this->argument('model');

        if ($modelClass === null) {
            $allModels = $this->helper()->getAllModelClasses();
            $modelClass = $this->choice('Choose model to show the fields from:', $allModels);
        }

        if (!class_exists($modelClass)) {
            throw new \Exception("Model '$modelClass' doesn`t exist");
        }

        return ltrim($modelClass, '\\');
    }
    protected function tableData($modelClass)
    {
        /** @var Model $model */
        $model = new $modelClass;
        $casts = $model->getCasts();
        $fillable = $model->getFillable();
        $guarded = $model->getGuarded();

        $database = config('database.connections.' . config('database.default') . '.database');
        // Array with: name, position, type, nullable, default_value
        $columns = $this->dbHandler()->getColumns($database, $model->getTable());

        $data = [];
        foreach ($columns as $i => $column) {
            $data[$i] = [
                $column['name'],
                $column['type'],
                $column['nullable'] ? 'YES' : '',
                $column['default_value'] ?? '',
                $casts[$column['name']] ?? '', // Casts
                in_array($column['name'], $guarded) ? 'YES' : '', // Guarded
                in_array($column['name'], $fillable) ? 'YES' : '' // Fillable
            ];
        }

        return $data;
    }


}
