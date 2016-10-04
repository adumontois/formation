<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 15:10
 */

namespace OCFram;


abstract class Validator
{
    protected $errorMessage;

    public function __construct($errorMessage = '')
    {
        $this -> setErrorMessage($errorMessage);
    }

    abstract public function isValid($value);

    public function errorMessage()
    {
        return $this -> errorMessage;
    }

    public function setErrorMessage($errorMessage)
    {
        if (is_string($errorMessage))
        {
            $this -> errorMessage = $errorMessage;
        }
        else
        {
            throw new \InvalidArgumentException('ErrorMessage must be a string');
        }
    }
}