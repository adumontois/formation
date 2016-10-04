<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 14:40
 */

namespace OCFram;

class TextField
{
    protected $cols;
    protected $rows;

    public function buildWidget()
    {
        $code = '';
        if (!empty($this -> errorMessage))
        {
            $code = $this -> errorMessage.'<br />';
        }
        $code .= '<label>'.$this -> label.'</label>
            <textarea name = "'.$this -> name().'" ';
        if (!empty($this -> cols))
        {
            $code .= 'cols = "'.$this -> cols.'" ';
        }
        if (!empty($this -> rows))
        {
            $code .= 'rows = "'.$this -> rows.'" ';
        }
        return $code.'>'.htmlspecialchars($this -> value()).'</textarea>';
    }

    public function setCols($cols)
    {
        if (is_int($cols) AND $cols >= 0)
        {
            $this -> cols = $cols;
        }
    }

    public function setRows($rows)
    {
        if (is_int($rows) AND $rows >= 0)
        {
            $this -> rows = $rows;
        }
    }
}