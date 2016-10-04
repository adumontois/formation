<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 12:42
 */

namespace OCFram;


trait Hydrator
{
    public function hydrate(array $values)
    {
        foreach ($values as $key => $argument)
        {
            $method = 'set'.ucfirst($key);
            if (is_callable(array($this, $method)))
            {
                $this -> $method($argument);
            }
        }
    }
}