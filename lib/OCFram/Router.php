<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 30/09/2016
 * Time: 16:41
 */

namespace OCFram;


class Router
{
    protected $routes;

    const ROUTE_NOT_FOUND = 18;

    public function __construct()
    {
        $this -> routes = array();
    }

    public function addRoute(Route $route)
    // Ajoute la route au catalogue du routeur, si elle n'existe pas déjà
    {
        if (!in_array($route, $this -> routes))
        {
            $this -> routes[] = $route;
        }
    }

    public function getRoute($url)
    {
        // Trouver la route qui matche l'url fournie
        foreach ($this -> routes as $iterator_route)
        {
            $varsValues = $iterator_route -> match($url)
            if ($varsValues !== false AND $iterator_route -> hasVars())
            // Si on a des variables, on doit les récupérer pour les faire transiter dans l'URL
            {
                $varsNames = $route -> varsNames();
                $listVars = array();
                foreach ($varsValues as $key => $value)
                // Récupérer les valeurs des attributs en clé-valeur entre $varsNames et $varsValues
                {
                    if ($key > 0)
                    // Le premier retour de preg_match est la chaîne complète
                    {
                        $listVars[$varsNames[$key - 1]] = $value;
                    }
                }
                $route -> setVars($listVars);
                return $route;
            }
        }

        // On n'a pas trouvé d'erreur
        throw new \RuntimeException('Couldn\'t find route '.$url.', no such route exists !', Router::ROUTE_NOT_FOUND);
    }
}