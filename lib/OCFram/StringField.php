<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 14:33
 */

namespace OCFram;


class StringField extends Field
{
    protected $maxLength;

    public function buildWidget()
    {
        $code = '';
        if (!empty($this -> errorMessage))
        {
            $code = $this -> errorMessage.'<br />';
        }
        $code .= '<label>'.$this -> label.'</label>
            <input type = "text" name = "'.$this -> name().'" value = "'.htmlspecialchars($this -> value()).'" ';
        if (!empty($this -> maxLength))
        {
            $code .= 'maxlength = "' . $this->maxLength.'" ';
        }
        return $code.'/>';
    }

    public function setMaxLength($maxLength)
    {
        if (is_int($maxLength) AND $maxLength > 0)
        {
            $this -> maxLength = $maxLength;
        }
        else
        {
            throw new \InvalidArgumentException('MaxLength must be a positive integer');
        }
    }
}