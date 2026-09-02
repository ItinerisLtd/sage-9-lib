<?php

declare(strict_types=1);

namespace Roots\Sage\Template;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\ViewServiceProvider;
use Roots\Sage\Container as SageContainer;

class BladeProvider extends ViewServiceProvider
{
    public function __construct(?SageContainer $container = null, array $config = [])
    {
        parent::__construct($container ?: SageContainer::getInstance());

        Container::setInstance($this->app);

        $this->app->bindIf('config', function () use ($config) {
            return new Repository($config);
        }, true);
    }

    public function register()
    {
        $this->registerFilesystem();
        $this->registerEvents();
        parent::register();
        return $this;
    }

    public function registerFilesystem()
    {
        $this->app->bindIf('files', Filesystem::class, true);
        return $this;
    }

    public function registerEvents()
    {
        $this->app->bindIf('events', Dispatcher::class, true);
        return $this;
    }

    public function registerViewFinder()
    {
        $this->app->bindIf('view.finder', function ($app) {
            $config = $this->app['config'];
            $paths = $config['view.paths'];
            $namespaces = $config['view.namespaces'];
            $finder = new FileViewFinder($app['files'], $paths);
            array_map([$finder, 'addNamespace'], array_keys($namespaces), $namespaces);
            return $finder;
        }, true);
        return $this;
    }
}
