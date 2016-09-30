<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 30/09/2016
 * Time: 16:43
 */

namespace OCFram;


class Route
{
    protected $action;
    protected $module;
    protected $url;
    protected $varsNames;
    protected $vars;

    public function __construct($url, $module, $action, $varsNames)
    {
        $this -> setAction($action);
        $this -> setModule($module);
        $this -> setUrl($url);
        $this -> setVarsNames($varsNames);
        $this -> setVars(array());
    }

    public function hasVars()
    // Indique si la route a des variables
    {
        return !empty($this -> varsNames);
    }

    public function match($url)
    // Vérifie si l'url fournie correspond à l'url de la route (présente dans le fichier routes.xml)
    // Si oui, renvoie les paramètres passés à la route
    {
        if (preg_match('%^'.$this -> url.'$%', $url, $matches))
        {
            return $matches;
        }
        return false;
    }

    // Setters
    public function setAction($action)
    {
        if (is_string($action))
        {
            $this -> action = $action;
        }
    }

    public function setModule($module)
    {
        if (is_string($module))
        {
            $this -> module = $module;
        }
    }

    public function setUrl($url)
    {
        if (is_string($url))
        {
            $this -> url = $url;
        }
    }

    public function setVarsNames(array $varsNames)
    {
        $this -> varsNames = $varsNames;
    }

    public function setVars(array $vars)
    {
        $this -> vars = $vars;
    }

    // Getters
    public function action()
    {
        return $this -> action;
    }

    public function module()
    {
        return $this -> module;
    }

    public function url()
    {
        return $this -> url;
    }

    public function varsNames()
    {
        return $this -> varsNames;
    }

    public function vars()
    {
        return $this -> vars;
    }
}