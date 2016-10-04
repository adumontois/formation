<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 15:21
 */

namespace OCFram;


class NotNullValidator extends Validator
{
    public function isValid($value)
    {
        return $value != '';
    }
}