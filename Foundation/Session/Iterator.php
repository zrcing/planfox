<?php
/**
 * @author Liao Gengling <liaogling@gmail.com>
 */
namespace Planfox\Foundation\Session;

class Iterator implements \Iterator
{
    /**
     * @var array list of keys in the map
     */
    private $keys;

    /**
     * @var mixed current key
     */
    private $key;

    public function __construct()
    {
        $this->keys = array_keys($_SESSION);
    }

    public function current()
    {
        return isset($_SESSION[$this->key]) ? $_SESSION[$this->key] : null;
    }

    public function next()
    {
        $this->key = next($this->keys);
    }

    public function rewind()
    {

        $this->key=reset($this->keys);
    }

    public function key()
    {
        return $this->key;
    }

    public function valid()
    {
        return $this->key !== false;
    }
}