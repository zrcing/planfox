<?php
/**
 * @author Liao Gengling <liaogling@gmail.com>
 */
namespace Planfox\Foundation;

use Planfox\Container\Container;

class Application extends Container
{
    /**
     * The base path for the Laravel installation.
     *
     * @var string
     */
    protected $basePath;

    protected $usageConfigComponent;

    public function __get($name)
    {
        return $this->make($name);
    }

    /**
     * Create a new Planfox application instance.
     *
     * @param  string|null  $basePath
     * @param boolean $usageConfigComponent
     */
    public function __construct($basePath = null, $usageConfigComponent = true)
    {
        if ($basePath) {
            $this->setBasePath($basePath);
        }
        $this->setUsageConfigComponent($usageConfigComponent);
        $this->registerBaseBindings();
        $this->registerCoreContainer();
        $this->registerCoreContainerAliases();
        $this->bootstrap();
    }

    /**
     * Set the base path for the application.
     *
     * @param  string  $basePath
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');
        return $this;
    }

    public function setUsageConfigComponent($usageConfigComponent)
    {
        $this->usageConfigComponent = $usageConfigComponent;
    }

    public function configPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'config';
    }

    /**
     * Register the basic bindings into the container.
     *
     */
    protected function registerBaseBindings()
    {
        static::setInstance($this);
    }

    public function registerCoreContainer()
    {
        $modules = [
            \Planfox\Foundation\Request\Repository::class,
            \Planfox\Foundation\Session\Session::class
        ];
        $this->addBinding($modules);
    }

    public function registerCoreContainerAliases()
    {
        $aliases = [
            'request' => \Planfox\Foundation\Request\Repository::class,
            'session' => \Planfox\Foundation\Session\Session::class
        ];

        $this->addAlias($aliases);
    }

    public function bootstrap()
    {
        if ($this->usageConfigComponent) {
            $this->singleton(\Planfox\Component\Config\Repository::class, function () {
                $config = new \Planfox\Component\Config\Repository();
                $config->setDirectory($this->configPath());
                return $config;
            }, 'config');

            $this->addBinding($this->make('config')->get('app.modules'));

            $this->addAlias($this->make('config')->get('app.aliases'));
        }
    }
}