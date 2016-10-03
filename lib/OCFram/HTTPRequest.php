<?php

namespace OCFram;

class HTTPRequest extends ApplicationComponent
{
	public function cookieData($key)
	// Retourne le contenu d'un cookie, ou NULL s'il n'est pas défini
	{
		if ($this -> cookieExists($key))
		{
			return (string) $_COOKIE[$key];
		}
		return NULL;
	}

	public function cookieExists($key)
	// Renvoie un booléen selon que le cookie soit défini ou non
	{
		return isset($_COOKIE[$key]);
	}

    public function getExists($key)
        // Renvoie un booléen selon que la clé donnée soit assignée en méthode GET ou non
    {
        return isset($_GET[$key]);
    }

	public function getData($key)
	// Retourne la variable passée en méthode GET demandée, ou NULL si elle n'est pas définie
	{
		if ($this -> getExists($key))
		{
			return (string) $_GET[$key];
		}
		return NULL;
	}
	
	public function method()
	// Renvoie la méthode d'accès pour envoyer la requête
	{
	    if (isset($_SERVER['REQUEST_METHOD']))
        {
            return $_SERVER['REQUEST_METHOD'];
        }
        return NULL;
	}

    public function postExists($key)
    // Renvoie un booléen selon que la clé donnée soit assignée en méthode POST ou non
    {
        return isset($_POST[$key]);
    }

    public function postData($key)
        // Retourne la variable passée en méthode POST demandée, ou NULL si elle n'est pas définie
    {
        if ($this -> postExists($key))
        {
            return (string) $_POST[$key];
        }
        return NULL;
    }

    public function requestURI()
    // Renvoie l'URI associée à la requête
    {
        if (isset($_SERVER['REQUEST_URI']))
        {
            return $_SERVER['REQUEST_URI'];
        }
        return NULL;
    }
}

