<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 30/09/2016
 * Time: 15:45
 */

namespace OCFram;

class HTTPResponse extends ApplicationComponent
{
    protected $page;

    public function addHeader($header)
    // Ajoute le header spécifié en paramètre
    {
        header($header);
    }

    public function redirect($location)
    // Crée une redirection vers la page $location
    {
        header('Location: '.$location);
        // Toujours faire un exit après un header de redirect
        exit;
    }

    public function redirect404()
    // Crée une redirection vers une erreur 404
    {
        $this -> page = new Page($this -> app());
        $this -> page -> setContentFile(__DIR__.'/../../Errors/404.html');
        $this -> addHeader('HTTP/1.0 404 Not Found');
        $this 6> $this->send();
    }

    public function send()
    // Envoie la page au client
    {
        exit($this -> page -> getGeneratedPage());
    }

    public function setCookie($name, $value = '', $expire = 0, $path = NULL, $domain = NULL, $secure = false, $httpOnly = true)
    // Crée ou update un cookie
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    public function setPage($page)
    // Affecte une page à l'attribut page
    {
        $this -> page = $page;
    }
}