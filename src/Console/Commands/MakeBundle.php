<?php

namespace PaulVL\JsonApi\Console\Commands;

use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use PaulVL\Helpers\StringHelper;

class MakeBundle extends Command
{
    use AppNamespaceDetectorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json-api:make-bundle {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new json-api controller and corresponding model classes';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Bundle's model name
     * @var string
     */
    private $model_name;

    /**
     * Bundle's controller name
     * @var string
     */
    private $controller_name;

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
        $this->model_name = 'Models\\' . $this->argument('name');
        $this->controller_name = '\\API\\' . $this->argument('name') . 'Controller';

        $this->info('creating json-api controller and model bundle . . .');

        $this->makeModel();
        $this->makeController();

        $this->info('json-api bundle created successfully ! ! !');
    }

    /**
     * Generate the desired migration.
     */
    protected function makeModel()
    {
        $name = $this->model_name;

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
    protected function getModelStub()
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
        $stub = $this->files->get($this->getModelStub());

        $this->replaceModelClassName($stub)->replaceModelNamespace($stub);

        return $stub;
    }

    /**
     * Replace the class name in the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceModelClassName(&$stub)
    {
        $className = ucwords( str_replace( '\\', '', strrchr( $this->model_name, '\\' ) ) );

        $stub = str_replace( '{{class}}', $className, $stub );

        return $this;
    }

    /**
     * Replace the namespace in the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceModelNamespace(&$stub)
    {
        $name = $this->model_name;
        $className = ucwords( str_replace( '\\', '', strrchr( $this->model_name, '\\' ) ) );
        $name = StringHelper::str_lreplace('\\', '', str_replace($className, '', $name));
        $namespace = $this->getAppNamespace().$name;

        $stub = str_replace('{{namespace}}', $namespace, $stub);

        return $this;
    }

    /**
     * Generate the desired migration.
     */
    protected function makeController()
    {
        $name = $this->controller_name;

        if ($this->files->exists($path = $this->getControllerPath($name))) {
            return $this->error('Controller already exists!');
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->compileControllerStub());

        $classname = ucwords( str_replace( '\\', '', strrchr( $this->controller_name, '\\' ) ) );
        $classname_lc = strtolower($classname);

        $this->info('json-api controller created successfully.');
        $this->info("   Don not forget add:");
        $this->info("   Route::resource('$classname_lc', '$classname', ['except' => ['create', 'edit']]);");
        $this->info("   To your routes.php file.");
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getControllerStub()
    {
        return __DIR__.'/stubs/controller_bundle.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string $name
     * @return string
     */
    protected function getControllerPath($name)
    {
        $name = str_replace($this->getAppNamespace(), '', $name);

        return $this->laravel['path'] . '\\Http\\Controllers\\' . $name . '.php';
    }

    /**
     * Compile the model stub.
     *
     * @return string
     */
    protected function compileControllerStub()
    {
        $stub = $this->files->get($this->getControllerStub());

        $this->replaceControllerClassName($stub)->replaceControllerModelClassName($stub)->replaceControllerNamespace($stub);

        return $stub;
    }

    /**
     * Replace the class name in the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceControllerClassName(&$stub)
    {
        $className = ucwords( str_replace( '\\', '', strrchr( $this->controller_name, '\\' ) ) );

        $stub = str_replace( '{{class}}', $className, $stub );

        return $this;
    }

    /**
     * Replace the class name in the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceControllerModelClassName(&$stub)
    {
        $className = ucwords( str_replace( '\\', '', strrchr( $this->model_name, '\\' ) ) );

        $name = $this->model_name;
        $name = StringHelper::str_lreplace('\\', '', str_replace($className, '', $name));
        $namespace = $this->getAppNamespace().$name;

        $stub = str_replace( '{{model_class}}', $namespace . '\\' . $className, $stub );

        return $this;
    }

    /**
     * Replace the namespace in the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceControllerNamespace(&$stub)
    {
        $name = $this->controller_name;
        $className = ucwords( str_replace( '\\', '', strrchr( $this->controller_name, '\\' ) ) );
        $name = StringHelper::str_lreplace('\\', '', str_replace($className, '', $name));
        $namespace = $this->getAppNamespace() . 'Http\\Controllers' . $name;

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