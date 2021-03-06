<?php

namespace Maatwebsite\Clerk\Templates\Adapters\Blade;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

class BladeEngine
{
    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * Array containing paths where to look for blade files.
     *
     * @var array
     */
    public $viewPaths;

    /**
     * Location where to store cached views.
     *
     * @var string
     */
    public $cachePath;

    /**
     * Initialize class.
     *
     * @param array  $viewPaths
     * @param string $cachePath
     */
    public function __construct($viewPaths = [], $cachePath)
    {
        $this->files     = new FileSystem();
        $this->viewPaths = (array) $viewPaths;
        $this->cachePath = $cachePath;
    }

    /**
     * Get the factory.
     */
    public function getFactory()
    {
        $resolver = $this->getEngineResolver();

        $finder = new FileViewFinder(
            $this->files,
            $this->viewPaths
        );

        return new Factory(
            $resolver,
            $finder,
            new Dispatcher()
        );
    }

    /**
     * Register the engine resolver instance.
     *
     * @return EngineResolver
     */
    public function getEngineResolver()
    {
        $resolver = new EngineResolver();

        // Add PhpEngine
        $resolver->register('php', function () {
            return new PhpEngine();
        });

        // Add Blade compiler engine
        $resolver->register('blade', function () {
            $compiler = new BladeCompiler(
                $this->files,
                $this->cachePath
            );

            return new CompilerEngine(
                $compiler
            );
        });

        return $resolver;
    }
}
