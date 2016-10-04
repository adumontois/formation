<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 15:49
 */

namespace OCFram;


abstract class FormBuilder
{
    protected $form;

    public function __construct(Entity $entity)
    {
        $this -> setForm(new Form($entity));
    }

    abstract public function build();

    public function form()
    {
        return $this -> form;
    }

    public function setForm(Form $form)
    {
        $this -> form = $form;
    }
}