<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 15:13
 */

namespace OCFram;


class MaxLengthValidator extends Validator
{
    protected $maxLength;

    public function __construct($errorMessage, $maxLength)
    {
        parent::__construct($errorMessage);
        $this -> setMaxLength($maxLength);
    }

    public function isValid($value)
    {
        return strlen($value) <= $this -> maxLength();
    }

    public function setMaxLength($value)
    {
        $maxLength = (int) $value;
        if ($maxLength > 0)
        {
            $this -> maxLength = $maxLength;
        }
        else
        {
            throw new \RuntimeException('MaxLength must be a positive integer');
        }
    }
}