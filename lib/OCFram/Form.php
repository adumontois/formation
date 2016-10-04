<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 12:39
 */

namespace OCFram;


class Form
{
    protected $entity;
    protected $fields;

    public function __construct(Entity $entity)
    {
        $this -> setEntity($entity);
        $this -> fields = array();
    }

    public function add(Field $field)
    {
        if ($field -> isValid())
        {
            $this -> fields[$field -> name()] = $field;
        }
        else
        {
            throw new \InvalidArgumentException('Can\'t add non-valid field to form');
        }
    }

    public function createView()
    {
        if ($this -> isValid())
        {
            $view = '<form method = "POST" action = "">';
            // Construire tous les fields
            foreach ($fields as $field)
            {
                $view .= $field -> buildWidget();
            }
            return $view.'</form>';
        }
        else
        {
            throw new \RuntimeException('Can\'t generate invalid form');
        }
    }

    public function isValid()
    {
        return !empty($fields);
    }

    public function entity()
    {
        return $this -> entity;
    }

    public function setEntity(Entity $entity)
    {
        $this -> entity = $entity;
    }
}