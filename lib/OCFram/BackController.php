<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 09:56
 */

namespace OCFram;

abstract class BackController extends ApplicationComponent
{
    protected $action;
    protected $module;
    protected $page;
    protected $view;
    protected $managers;

    public function __construct(Application $app, $module, $action)
    // Construit un backController comme une composant de l'application
    // Le backController est associé à une action et un module, et construit une vue.
    {
        parent::__construct($app);
        $this -> setAction($action);
        $this -> setModule($module);
        $this -> page = new Page($app);
        $this -> setView($action);

        $this -> managers = new Managers('PDO', PDOFactory::getMysqlConnexion());
    }

    public function execute()
    {
        $method = 'execute'.ucfirst($this -> action);
        if (!is_callable([$this, $method]))
        {
            throw new \RuntimeException('Undefined action "'.$this -> action.'" on this module');
        }
        $this -> $method($this -> app -> httpRequest());
    }

    public function page()
    {
        return $this -> page;
    }

    public function setModule($module)
    {
        if (!is_string($module) || empty($module))
        {
            throw new \InvalidArgumentException('Module must be a valid string');
        }
        $this -> module = $module;
    }

    public function setAction($action)
    {
        if (!is_string($action) || empty($action))
        {
            throw new \InvalidArgumentException('Action must be a valid string');
        }
        $this -> action = $action;
    }

    public function setView($view)
    {
        if (!is_string($view) || empty($view))
        {
            throw new \InvalidArgumentException('View must be a valid string');
        }
        $this -> view = $view;
        $this -> page -> setContentFile(__DIR__.'/../../App/'.$this -> app -> name().'/Modules/'.$this -> module.'/Views/'.$this -> view.'.php');
    }
}