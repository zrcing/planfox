<?php
/**
 * @author Liao Gengling <liaogling@gmail.com>
 */
namespace Planfox\Container;

abstract class Provider
{
    /**
     * 面向服务注册方法
     */
    abstract public function register();
}