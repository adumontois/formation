<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 12:42
 */

namespace OCFram;


abstract class Field
{
    use Hydrator;

    protected $errorMessage;
    protected $label;
    protected $name;
    protected $value;

    public function __construct(array $options = array())
    {
        $this -> hydrate($options);
    }

    // Construit le champ et renvoie la string à transmettre en page
    abstract public function buildWidget();

    public function isValid()
    {
        return is_string($this -> label) AND !empty($this -> label)
            AND is_string($this -> name) AND !empty($this -> name) AND substr($this -> name, 0, 1);
    }

    public function label()
    {
        return $this -> label;
    }

    public function name()
    {
        return $this -> name;
    }

    public function value()
    {
        return $this -> value;
    }

    public function setLabel($label)
    {
        if (is_string($label))
        {
            $this -> label = $label;
        }
    }

    public function setName($name)
    {
        if (is_string($name))
        {
            $this -> name = $name;
        }
    }

    public function setValue($value)
    {
        if (is_string($value))
        {
            $this -> value = $value;
        }
    }
}