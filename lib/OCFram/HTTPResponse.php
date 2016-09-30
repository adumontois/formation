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
        if (is_string($header))
        {
            header($header);
        }
    }

    public function redirect($location)
    // Crée une redirection vers la page $location
    {
        if (is_string($location))
        {
            header('Location: '.$location);
            // Toujours faire un exit après un header de redirect
            exit;
        }
    }

    public function redirect404()
    // Crée une redirection vers une erreur 404
    {
        // $this->redirect($_SERVER['SERVER_PROTOCOL'] + ' 404 Not Found');
    }

    public function send()
    // Envoie la page au client
    {
        exit($this -> page -> getGeneratedPage());
    }

    public function setCookie($name, $value = '', $expire = 0, $path = NULL, $domain = NULL, $secure = false, $httpOnly = true)
    // Crée ou update un cookie
    {
        if (!empty($name) AND is_string($name))
        {
            setCookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
        }
    }

    public function setPage($page)
    // Affecte une page à l'attribut page
    {
        $this -> page = $page;
    }
}