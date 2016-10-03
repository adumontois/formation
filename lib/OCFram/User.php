<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 12:03
 */

namespace OCFram;

// Besoin de démarrer la session USER systématiquement avec la classe
session_start();

class User extends ApplicationComponent
{
    public function getAttribute($attr)
    {
        if (isset($_SESSION[$attr]))
        {
            return $_SESSION[$attr];
        }
        return NULL;
    }

    public function getFlash()
    {
        $flash = $this -> getAttribute('flash');
        unset($_SESSION['flash']);
        return $flash;
    }

    public function hasFlash()
    {
        return isset($_SESSION['flash']);
    }

    public function isAuthenticated()
    {
        return isset($_SESSION['auth']) AND $_SESSION['auth'] === true;
    }

    public function setAttribute($attr, $value)
    {
        $_SESSION[$attr] = $value;
    }

    public function setAuthenticated($authenticated = true)
    {
        if (!is_bool($authenticated))
        {
            throw new \InvalidArgumentException('Authentication value must be a true-or-false boolean');
        }
        $this -> setAttribute('auth', $authenticated);
    }

    public function setFlash($value)
    {
        $this -> setAttribute('flash', $value);
    }
}