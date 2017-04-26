<?php
/**
 * @author Liao Gengling <liaogling@gmail.com>
 */
namespace Planfox\Foundation\Session;

use Planfox\Foundation\Session\Exception\Exception;

class Session implements \Countable, \ArrayAccess, \IteratorAggregate
{
    use ArrayAccess;

    protected $autoStart=true;

    public function __construct()
    {
        if($this->autoStart)
            $this->open();
        register_shutdown_function(array($this,'close'));
    }

    public function setAutoStart($autoStart = true)
    {
        $this->autoStart = $autoStart;
    }

    public function getUseCustomStorage()
    {
        return false;
    }

    public function open()
    {
        if($this->getUseCustomStorage())
        @session_set_save_handler(array($this,'openSession'),array($this,'closeSession'),array($this,'readSession'),array($this,'writeSession'),array($this,'destroySession'),array($this,'gcSession'));

        @session_start();
        if (\Planfox::getDebug() && session_id()=='') {
            if (function_exists('error_get_last')) {
                $message = error_get_last();
            } else {
                $message = 'Failed to start session.';
            }
            throw new Exception($message);
        }
    }

    public function close()
    {
        if(session_id()!=='')
            @session_write_close();
    }

    public function destroy()
    {
        if(session_id()!=='')
        {
            @session_unset();
            @session_destroy();
        }
    }

    public function getIsStarted()
    {
        return session_id()!=='';
    }

    public function getSessionID()
    {
        return session_id();
    }

    public function setSessionID($value)
    {
        session_id($value);
    }

    public function regenerateID($deleteOldSession=false)
    {
        if($this->getIsStarted())
            session_regenerate_id($deleteOldSession);
    }

    public function getSessionName()
    {
        return session_name();
    }

    public function setSessionName($value)
    {
        session_name($value);
    }

    public function getSavePath()
    {
        return session_save_path();
    }

    public function setSavePath($value)
    {
        if(is_dir($value))
            session_save_path($value);
        else
            throw new Exception("Session.savePath \"{$value}\" is not a valid directory.");
    }

    public function getCookieParams()
    {
        return session_get_cookie_params();
    }

    public function setCookieParams($value)
    {
        $data=session_get_cookie_params();
        extract($data);
        extract($value);
        if(isset($httponly))
            session_set_cookie_params($lifetime,$path,$domain,$secure,$httponly);
        else
            session_set_cookie_params($lifetime,$path,$domain,$secure);
    }

    /**
     * @return string how to use cookie to store session ID. Defaults to 'Allow'.
     */
    public function getCookieMode()
    {
        if(ini_get('session.use_cookies')==='0')
            return 'none';
        elseif(ini_get('session.use_only_cookies')==='0')
            return 'allow';
        else
            return 'only';
    }

    /**
     * @param string $value how to use cookie to store session ID. Valid values include 'none', 'allow' and 'only'.
     * @throws
     */
    public function setCookieMode($value)
    {
        if($value==='none')
        {
            ini_set('session.use_cookies','0');
            ini_set('session.use_only_cookies','0');
        }
        elseif($value==='allow')
        {
            ini_set('session.use_cookies','1');
            ini_set('session.use_only_cookies','0');
        }
        elseif($value==='only')
        {
            ini_set('session.use_cookies','1');
            ini_set('session.use_only_cookies','1');
        }
        else
            throw new Exception('Session.cookieMode can only be "none", "allow" or "only".');
    }

    /**
     * @return float the probability (percentage) that the gc (garbage collection) process is started on every session initialization, defaults to 1 meaning 1% chance.
     */
    public function getGCProbability()
    {
        return (float)(ini_get('session.gc_probability')/ini_get('session.gc_divisor')*100);
    }

    /**
     * @param float $value the probability (percentage) that the gc (garbage collection) process is started on every session initialization.
     * @throws Exception if the value is beyond [0,100]
     */
    public function setGCProbability($value)
    {
        if($value>=0 && $value<=100)
        {
            // percent * 21474837 / 2147483647 ≈ percent * 0.01
            ini_set('session.gc_probability',floor($value*21474836.47));
            ini_set('session.gc_divisor',2147483647);
        }
        else
            throw new Exception('Session.gcProbability "{'.$value.'}" is invalid. It must be a float between 0 and 100.');
    }

    /**
     * @return boolean whether transparent sid support is enabled or not, defaults to false.
     */
    public function getUseTransparentSessionID()
    {
        return ini_get('session.use_trans_sid')==1;
    }

    /**
     * @param boolean $value whether transparent sid support is enabled or not.
     */
    public function setUseTransparentSessionID($value)
    {
        ini_set('session.use_trans_sid',$value?'1':'0');
    }

    /**
     * @return integer the number of seconds after which data will be seen as 'garbage' and cleaned up, defaults to 1440 seconds.
     */
    public function getTimeout()
    {
        return (int)ini_get('session.gc_maxlifetime');
    }

    /**
     * @param integer $value the number of seconds after which data will be seen as 'garbage' and cleaned up
     */
    public function setTimeout($value)
    {
        ini_set('session.gc_maxlifetime',$value);
    }

    public function openSession($savePath,$sessionName)
    {
        return true;
    }

    public function closeSession()
    {
        return true;
    }

    public function readSession($id)
    {
        return '';
    }

    public function writeSession($id,$data)
    {
        return true;
    }

    public function destroySession($id)
    {
        return true;
    }

    public function gcSession($maxLifetime)
    {
        return true;
    }

    public function getCount()
    {
        return count($_SESSION);
    }

    public function count()
    {
        return $this->getCount();
    }

    public function getKeys()
    {
        return array_keys($_SESSION);
    }

    public function get($key,$defaultValue=null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $defaultValue;
    }

    public function itemAt($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function add($key,$value)
    {
        $_SESSION[$key]=$value;
    }

    public function remove($key)
    {
        if(isset($_SESSION[$key]))
        {
            $value=$_SESSION[$key];
            unset($_SESSION[$key]);
            return $value;
        }
        else
            return null;
    }

    public function clear()
    {
        foreach(array_keys($_SESSION) as $key)
            unset($_SESSION[$key]);
    }

    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public function toArray()
    {
        return $_SESSION;
    }



    public function getIterator()
    {
        return new Iterator();
    }
}