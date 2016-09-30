<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 30/09/2016
 * Time: 16:02
 */

namespace OCFram;

abstract class Application
{
    protected $httpRequest;
    protected $httpResponse;
    protected $name;

    public function __construct()
    // Construit un objet application en initialisant httpRequest et httpResponse
    {
        $this -> httpRequest = new HTTPRequest();
        $this -> httpResponse = new HTTPResponse();
        // A assigner dans les classes filles
        $this -> name = '';
    }

    // Methode permettant de lancer une application
    abstract public function run();

    // Accesseur httpRequest
    public function httpRequest()
    {
        return $this -> httpRequest;
    }

    // Accesseur httpResponse
    public function httpResponse()
    {
        return $this -> httpResponse;
    }

    // Accesseur name
    public function name()
    {
        return $this -> name;
    }
}