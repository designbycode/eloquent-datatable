<?php

namespace Designbycode\EloquentDatatable\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EloquentDatatableCommand extends Command
{
    protected $signature = 'datatable:create {name : The name of the controller} {--model= : The model associated with the controller} {--middleware= : The middleware to be applied (comma separated)}';

    protected $description = 'Create a DataTable controller';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $controllerName = $this->argument('name');
        $modelName = $this->option('model');
        $middleware = $this->option('middleware');

        // Read the stub file
        $stub = File::get(__DIR__.'/../../stubs/datatable.stub');

        if (! $modelName) {
            $modelName = $this->getSingularName($controllerName);
            if (! $this->confirm("Do you want to use '$modelName' as the model name?", true)) {
                $modelName = $this->ask('Enter the model name:');
            }
        }

        if ($this->confirm("Do you want to use '$modelName' model fillable fields for updating columns?", true)) {
            // Get the path to the model file
            $modelPath = $this->getModelPath($modelName);

            // Read the model file
            $modelContent = File::get($modelPath);

            // Extract the $fillable fields from the model content
            $fillable = $this->extractFillableFields($modelContent);

            $stub = str_replace('// :fillable', implode(',', $fillable), $stub);
        } else {
            $stub = str_replace('// :fillable', "''", $stub);
        }

        // Replace placeholders
        $stub = str_replace('StubController', $controllerName, $stub);
        $stub = str_replace('StubModel', $modelName, $stub);

        // Define namespace, path, class name, and file name
        $namespace = 'App\\Http\\Controllers\\Datatables';
        $path = app_path('Http\\Controllers\\Datatables');
        $className = $controllerName;
        $fileName = $className.'.php';

        // Ensure the directory exists
        File::ensureDirectoryExists($path);

        if ($middleware) {
            $middlewareArray = explode(',', $middleware);
            $middlewareCode = "\$this->middleware(['".implode("','", $middlewareArray)."']);";
            $stub = str_replace('// :middleware', $middlewareCode, $stub);
        }

        //         Check if model exists
        if (! $this->modelExists($modelName)) {
            $this->error("Model '$modelName' does not exist in App\Models directory.");

            return;
        }

        // Check if the file already exists
        if (File::exists($path.'\/'.$fileName)) {
            $this->error('Controller file already exists: '.$path.'\/'.$fileName);

            return;
        }

        // Write the content to the file
        File::put($path.'/'.$fileName, $stub);

        // Inform user about successful creation
        $this->info('DataTable controller created successfully: '.$path.'/'.$fileName);
    }

    private function modelExists($modelName): bool
    {
        $modelFilePath = app_path('Models/'.$modelName.'.php');

        return File::exists($modelFilePath);
    }

    // Function to get the path to the model file
    private function getModelPath($modelName): string
    {
        return app_path('Models/'.$modelName.'.php');
    }

    // Function to extract $fillable fields from model content
    private function extractFillableFields($modelContent): array
    {
        $matches = [];
        preg_match('/\$fillable\s*=\s*\[(.*?)\];/s', $modelContent, $matches);
        if (isset($matches[1])) {
            return array_map('trim', explode(',', $matches[1]));
        }

        return [];
    }

    private function getSingularName($controller): string
    {
        return Str::singular($this->getPascalCaseFirstSegment($controller));
    }

    private function getPascalCaseFirstSegment(string $inputString): string
    {
        // Split the input string by uppercase letters to get the first segment
        preg_match('/^[A-Z][a-z]*/', $inputString, $matches);

        // Return the matched segment
        return $matches[0];
    }
}
