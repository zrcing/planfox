<?php
/**
 * @author Liao Gengling <liaogling@gmail.com>
 */
namespace Planfox\Container;

use Planfox\Exception\Exception;

class Container implements ContainerInterface
{
    /**
     * The current globally available container (if any).
     *
     * @var static
     */
    protected static $instance;

    protected $bindings = [];

    protected $instances = [];

    protected $aliases = [];

    /**
     * Set the globally available instance of the container.
     *
     * @return static
     *
     * @throws
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            throw new Exception('Container not initialized');
        }
        return static::$instance;
    }

    /**
     * Set the shared instance of the container.
     *
     * @param  $container
     * @return static
     */
    public static function setInstance($container = null)
    {
        return static::$instance = $container;
    }

    public function make($abstract, $closure = null)
    {
        if ($this->isAlias($abstract)) {
            $abstract = $this->alias($abstract);
        }
        if (! isset($this->instances[$abstract])) {
            if (is_null($closure)) {
                $object = new $abstract;
                if ($object instanceof Provider) {
                    $this->instances[$abstract] = $object->register();
                } else {
                    $this->instances[$abstract] = $object;
                }

            } else {
                $this->instances[$abstract] = call_user_func($closure);
            }
        }

        return $this->instances[$abstract];
    }

    public function singleton($abstract, $closure = null, $alias = null)
    {
        $this->addBinding([$abstract]);
        $this->make($abstract, $closure);
        if (! is_null($alias)) {
            $this->addAlias([$alias=>$abstract]);
        }
    }

    public function isBinding($abstract)
    {
        $abstract = $this->alias($abstract);
        return isset($this->bindings[$abstract]) ? true : false;
    }

    public function addBinding($bindings)
    {
        if (! is_array($bindings)) {
            throw new Exception('The error of the parameters');
        }
        foreach ($bindings as $v) {
            $this->bindings[$v] = $v;
        }

        return $this;
    }

    public function isAlias($abstract)
    {
        return isset($this->aliases[$abstract]) ? true : false;
    }

    public function alias($abstract)
    {
        return isset($this->aliases[$abstract]) ? $this->aliases[$abstract] : $abstract;
    }

    public function addAlias($aliases)
    {
        if (! is_array($aliases)) {
            throw new Exception('The error of the parameters');
        }
        foreach ($aliases as $k => $v) {
            $this->aliases[$k] = $v;
        }
        return $this;
    }

    public function getBindings()
    {
        return $this->bindings;
    }

    public function getAliases()
    {
        return $this->aliases;
    }

    public function getInstances()
    {
        return $this->instances;
    }
}