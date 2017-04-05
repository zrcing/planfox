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

    /**
     * Create a new Planfox application instance.
     *
     * @param  string\null  $basePath
     */
    public function __construct($basePath = null)
    {
        if ($basePath) {
            $this->setBasePath($basePath);
        }
        $this->registerBaseBindings();
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


    public function registerCoreContainerAliases()
    {
    }

    public function bootstrap()
    {
        $this->singleton(\Planfox\Component\Config\Repository::class, function(){
            $config = new \Planfox\Component\Config\Repository();
            $config->setDirectory($this->configPath());
            return $config;
        }, 'config');

        // 载入应用程序模块
        $this->addBinding($this->make('config')->get('app.modules'));

        // 载入应用程序模块别名
        $this->addAlias($this->make('config')->get('app.aliases'));
    }
}