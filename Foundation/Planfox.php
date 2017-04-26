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

    protected static $debug = false;

    public static function createApplication($basePath, $usageConfigComponent = true)
    {
        if (is_null(self::$app)) {
            self::$app = new \Planfox\Foundation\Application($basePath, $usageConfigComponent);
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

    public static function setDebug($debug = false)
    {
        self::$debug = $debug;
    }

    public static function getDebug()
    {
        return self::$debug;
    }
}