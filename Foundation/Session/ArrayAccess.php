<?php
/**
 * @author Liao Gengling <liaogling@gmail.com>
 */
namespace Planfox\Foundation\Session;

trait ArrayAccess
{
    public function offsetExists($offset)
    {
        return isset($_SESSION[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($_SESSION[$offset]) ? $_SESSION[$offset] : null;
    }

    public function offsetSet($offset,$item)
    {
        $_SESSION[$offset]=$item;
    }

    public function offsetUnset($offset)
    {
        unset($_SESSION[$offset]);
    }
}