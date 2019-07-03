<?php

namespace BinaryTorch\UpdatableModel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeUpdatableModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:updatable {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new updatable model.';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * The construct.
     *
     * @param Filesystem $filesystem
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Generating a new updatable resource...');

        $className = $this->argument('model') . 'UpdatableModel';
        $updatableDir = app_path('UpdatableModels');
        $updatablePath = app_path('UpdatableModels/') . $className . '.php';
        
        if (! $this->filesystem->isDirectory($updatableDir)) {
            $this->filesystem->makeDirectory($updatableDir, 0755, true);
        }

        if (! $this->filesystem->exists($updatablePath)) {
            $content = $this->getClassContentFromStub($className);

            $this->filesystem->put($updatablePath, $content);

            $this->info('Done.');
        }else {
            $this->error('File Already Exists!');
        }
    }

    /**
     * @return string
     */
    protected function getClassContentFromStub($class)
    {
        return str_replace(
            '{{CLASSNAME}}',
            $class,
            $this->getStubContent()
        );
    }

    /**
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getStubContent()
    {
        return $this->filesystem->get(base_path('/vendor/binarytorch/updatable-model/stubs/UpdatableModel.stub'));
    }
}