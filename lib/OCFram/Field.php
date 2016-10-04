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
    protected $validators;

    public function __construct(array $options = array())
    {
        $this -> setValidators(array());
        $this -> hydrate($options);
    }

    // Construit le champ et renvoie la string à transmettre en page
    abstract public function buildWidget();

    public function isValid()
    {
        foreach ($this -> validators as $validator)
        {
            if (!$validator -> isvalid($this -> value))
            {
                $this -> errorMessage = $validator -> errorMessage();
                return false;
            }
        }
        return true;
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

    public function validators()
    {
        return $this -> validators;
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

    public function setValidators(array $validators)
    {
        // Ajouter les validateurs suivants
        foreach ($validators as $validator)
        {
            if (!in_array($validator, $this -> validators))
            {
                $this -> validators[] = $validators;
            }
        }
    }
}