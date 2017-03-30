<?php
/**
 * @author Liao Gengling <liaogling@gmail.com>
 */
class Planfox
{
    /**
     * @var \Planfox\Foundation\Application
     */
    protected static $app;

    public static function createApplication($basePath)
    {
        if (is_null(self::$app)) {
            self::$app = new \Planfox\Foundation\Application($basePath);
        }
        return self::$app;
    }

    /**
     * @param mixed string|null $module
     * @return mixed|\Planfox\Foundation\Application
     * @throws Exception
     */
    public static function app($module = null)
    {
        if (is_null(self::$app)) {
            throw new Exception("Application not initialized");
        }
        return is_null($module) ? self::$app : self::$app->make($module);
    }
}