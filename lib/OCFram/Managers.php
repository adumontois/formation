<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 10:27
 */

namespace OCFram;


class Managers
{
    protected $api;
    protected $dao;
    protected $managers;

    public function __construct($api, $dao)
    {
        $this -> api = $api;
        $this -> dao = $dao;
        $this -> managers = array();
    }

    public function getManagerOf($module)
    // Construit un manager à partir du module passé s'il n'est pas encore créé
    // Retourne le module du manager
    {
        if (!is_string($module) OR empty($module))
        {
            throw new \InvalidArgumentException('Module must be a valid string');
        }

        if (!isset($this -> managers[$module]))
        {
            $manager = '\\Model\\'.$module.'Manager'.$this -> api;
            $this -> managers[$module] = new $manager($this -> dao);
        }

        return $this -> managers[$module];
    }
}