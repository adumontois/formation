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
            $field_name = $field -> name(); // Récupérer le nom du champ
            $field -> setValue($this -> entity -> $field_name());
            // La value du field est initialisée à la valeur détenue par l'entité
            $this -> fields[] = $field;
            return $this;
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
            $view = '';
            // Construire tous les fields
            foreach ($this -> fields as $field)
            {
                $view .= $field -> buildWidget();
                $view .= '<br />';
            }
            return $view;
        }
        else
        {
            throw new \RuntimeException('Can\'t generate invalid form');
        }
    }

    public function isValid()
    {
        $valid = true;
        foreach ($this -> fields as $field)
        {
            if (!($valid = $valid AND $field -> isValid()))
            {
                break;
            }
        }
        return $valid;
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