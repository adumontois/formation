<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 10:53
 */

namespace OCFram;

abstract class Manager
// Classe représentant un manager type pour une classe
{
    protected $dao;

    public function __construct($dao)
    {
        $this -> dao = $dao;
    }
}