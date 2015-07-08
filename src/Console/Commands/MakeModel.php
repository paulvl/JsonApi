<?php

namespace PaulVL\JsonApi\Console\Commands;

use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use PaulVL\Helpers\StringHelper;

class MakeModel extends Command
{
    use AppNamespaceDetectorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json-api:make-model {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new json-api model class';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->makeModel();
    }

    /**
     * Generate the desired migration.
     */
    protected function makeModel()
    {
        $name = '\\'.$this->argument('name');

        if ($this->files->exists($path = $this->getModelPath($name))) {
            return $this->error('Model already exists!');
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->compileModelStub());

        $this->info('json-api model created successfully.');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/model.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string $name
     * @return string
     */
    protected function getModelPath($name)
    {
        $name = str_replace($this->getAppNamespace(), '', $name);

        return $this->laravel['path'] . '\\' . $name . '.php';
    }

    /**
     * Compile the model stub.
     *
     * @return string
     */
    protected function compileModelStub()
    {
        $stub = $this->files->get($this->getStub());

        $this->replaceClassName($stub)->replaceNamespace($stub);

        return $stub;
    }

    /**
     * Replace the class name in the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceClassName(&$stub)
    {
        $className = ucwords( str_replace( '\\', '', strrchr( '\\'.$this->argument('name'), '\\' ) ) );

        $stub = str_replace( '{{class}}', $className, $stub );

        return $this;
    }

    /**
     * Replace the namespace in the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceNamespace(&$stub)
    {
        $name = '\\'.$this->argument('name');
        $className = ucwords( str_replace( '\\', '', strrchr( '\\'.$this->argument('name'), '\\' ) ) );
        $name = StringHelper::str_lreplace('\\', '', str_replace($className, '', $name));
        $namespace = $this->getAppNamespace().$name;

        $stub = str_replace('{{namespace}}', $namespace, $stub);

        return $this;
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }
}