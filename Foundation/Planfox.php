<?php
/**
 * @author Liao Gengling <liaogling@gmail.com>
 */
use Planfox\Exception\Exception;

class Planfox
{
    /**
     * @var \Planfox\Foundation\Application
     */
    protected static $app;

    protected static $debug = false;

    public static function createApplication(
        $basePath,
        $usageConfigComponent = true,
        $configComponentDirectory = null)
    {
        if (is_null(self::$app)) {
            self::$app = new \Planfox\Foundation\Application(
                $basePath,
                $usageConfigComponent,
                $configComponentDirectory);
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
            throw new Exception("The application does not initialize.");
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