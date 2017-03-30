<?php
/**
 * @author Liao Gengling <liaogling@gmail.com>
 */
namespace Planfox\Container;

interface ContainerInterface
{
    /**
     * 实例化一个模块, 如果$closure存在，则使用$closure实例化
     *
     * @param string $abstract
     * @param \Closure $closure
     */
    public function make($abstract, $closure = null);

    /**
     * 实例化一个模块, 如果$closure存在，则使用$closure实例化
     * 此方法实例化会自动绑定模块
     *
     * @param string $abstract
     * @param \Closure $closure
     * @param string $alias 别名
     */
    public function singleton($abstract, $closure = null, $alias = null);

    /**
     * 判断模块是否已添加
     *
     * @param string $abstract
     */
    public function isBinding($abstract);

    /**
     * 添加新模块
     *
     * @param array $bindings [Module1::class, Module2::class]
     */
    public function addBinding($bindings);

    /**
     * 是否是别名
     *
     * @param string $abstract
     */
    public function isAlias($abstract);

    /**
     * 通过别名取得真实类名
     *
     * @param string $abstract
     */
    public function alias($abstract);

    /**
     * 添加别名
     *
     * @param array $aliases ['aliasName1' => Module1::class, 'aliasName2' => Module2::class]
     */
    public function addAlias($aliases);

    /**
     * 获取所有已注册的模块
     */
    public function getBindings();

    /**
     * 获取所有模块的别名
     */
    public function getAliases();

    /**
     * 获取所有已实例化的模块
     */
    public function getInstances();
}