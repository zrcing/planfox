<?php
/**
 * @author Liao Gengling <liaogling@gmail.com>
 */
namespace Planfox\Config;

use Planfox\Exception\Exception;

class Repository
{
    /**
     * @var string config root directory
     */
    protected $directory;

    protected $configures = [];

    public function setDirectory($directory)
    {
        $this->directory = $directory;
        $this->init();

        return $this;
    }

    /**
     * 初始化配置信息
     */
    protected function init()
    {
        if (is_null($this->directory)) {
            throw new Exception('Can\'t set root directory of configure');
        }
        $files = glob( $this->directory . DIRECTORY_SEPARATOR ."*.php");
        foreach ($files as $file) {
            if (is_file($file)) {
                $fileinfo = pathinfo($file);
                $this->configures[$fileinfo['filename']] = require $file;
            }
        }
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function get($key = null)
    {
        if (is_null($key)) {
            return $this->configures;
        }
        $info = explode('.', $key);
        $access = array_reduce($info, function ($r, $a) {
            return $r ? "{$r}['{$a}']" : "['{$a}']";
        });
        $code = "if(is_null(\$this->configures{$access})){\$r=null;}else{\$r=\$this->configures{$access};}";
        eval($code);

        return $r;
    }

    public function set($key, $vars)
    {
        if (is_null($key) || $key == '') {
            throw new Exception('Can\'t set configure, the key was wrong!');
        }
        $info = explode('.', $key);
        $access = array_reduce($info, function ($r, $a) {
            return $r ? "{$r}['{$a}']" : "['{$a}']";
        });
        $code = "\$this->configures{$access}=\$vars;";
        eval($code);
    }
}