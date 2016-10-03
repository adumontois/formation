<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 10:55
 */

namespace OCFram;

abstract class Entity implements \ArrayAccess
// Classe représentant une classe type
{
    protected $erreurs;
    protected $id;

    public function  __construct(array $values)
    {
        if (!empty($values))
        {
            $this -> hydrate($values);
        }
        $this -> erreurs = array();
    }

    public function hydrate($values)
    {
        foreach ($values as $key => $argument)
        {
            $method = 'set'.ucfirst($key);
            if (method_exists($method, self::class))
            {
                $this -> $method($argument);
            }
        }
    }

    public function object_new()
    {
        return empty($this -> id);
    }

    // Implementation de l'interface
    public function offsetExists($offset)
    {
        return isset($this -> $offset) AND is_callable(array($this, $offset));
    }

    public function offsetGet($offset)
    {
        if ($this -> offsetExists($offset))
        {
            return $this -> $offset();
        }
        return NULL;
    }

    public function offsetSet($offset, $value)
    {
        $method = 'set'.ucfirst($offset);
        if (isset($this -> $offset) AND is_callable(array([$this, $method])))
        {
            $this -> $method($value);
        }
    }

    public function offsetUnset($offset)
    {
        throw new \Exception('Can\'t delete value in entity');
    }

    // Getters et setters
    public function id()
    {
        return $this -> id;
    }

    public function erreurs()
    {
        return $this -> erreurs;
    }

    public function setId($id)
    {
        if (is_int($id) AND $id > 0)
        {
            $this -> id = $id;
        }
    }
}