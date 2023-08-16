<?php

namespace Native\Electron\Updater;

use InvalidArgumentException;

class UpdaterManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved updater providers.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Create a new Updater manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get a updater provider instance by name, wrapped in a repository.
     *
     * @param  string|null  $name
     * @return \Native\Electron\Updater\Contracts\Updater
     */
    public function provider($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->providers[$name] ??= $this->resolve($name);
    }

    /**
     * Get a updater provider instance.
     *
     * @param  string|null  $driver
     * @return \Native\Electron\Updater\Contracts\Updater
     */
    public function driver($driver = null)
    {
        return $this->store($driver);
    }

    /**
     * Resolve the given store.
     *
     * @param  string  $name
     * @return \Native\Electron\Updater\Contracts\Updater
     *
     * @throws \InvalidArgumentException
     */
    public function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("NativePHP updater provider [{$name}] is not defined.");
        }

        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        }

        throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
    }

    /**
     * Get the updater provider configuration.
     *
     * @param  string  $name
     * @return array|null
     */
    protected function getConfig($name)
    {
        if (! is_null($name) && $name !== 'null') {
            return $this->app['config']["nativephp.updater.providers.{$name}"];
        }

        return ['driver' => 'null'];
    }

    /**
     * Get the default updater driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['nativephp.updater.default'];
    }

    /**
     * Set the default updater driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['nativephp.updater.default'] = $name;
    }

    /**
     * Set the application instance used by the manager.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return $this
     */
    public function setApplication($app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Create an instance of the spaces updater driver.
     *
     * @return \Native\Electron\Updater\Contracts\Updater
     */
    protected function createSpacesDriver(array $config)
    {
        return new SpacesProvider($config);
    }

    /**
     * Create an instance of the spaces updater driver.
     *
     * @return \Native\Electron\Updater\Contracts\Updater
     */
    protected function createS3Driver(array $config)
    {
        return new S3Provider($config);
    }

    /**
     * Create an instance of the GitHub updater driver.
     *
     * @return \Native\Electron\Updater\Contracts\Updater
     */
    protected function createGitHubDriver(array $config)
    {
        return new GitHubProvider($config);
    }

    /**
     * Dynamically call the default updater instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->provider()->$method(...$parameters);
    }
}
