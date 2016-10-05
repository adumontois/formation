<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 30/09/2016
 * Time: 16:41
 */

namespace OCFram;

/**
 * Class Router
 * Modélise le routeur, chargé de la gestion des routes et de rediriger le client vers la bonne route pour récupérer la page souhaitée.
 *
 * @package OCFram
 */
class Router {
	/**
	 * @var $routes Route[]
	 */
	protected $routes;
	const ROUTE_NOT_FOUND = 18;
	
	/**
	 * Construit le routeur comme un tableau de 0 route.
	 */
	public function __construct() {
		$this->routes = array();
	}
	
	/**
	 * Ajoute la route au catalogue du routeur, si elle n'existe pas déjà.
	 *
	 * @param Route $route
	 */
	public function addRoute( Route $route ) {
		if ( !in_array( $route, $this->routes ) ) {
			$this->routes[] = $route;
		}
	}
	
	/**
	 * Récupère la route qui correspond à l'url fournie en paramètre.
	 * Une erreur d'exécution est renvoyée si la route n'existe pas.
	 *
	 * @param $url string
	 *
	 * @return Route
	 */
	public function getRoute( $url ) {
		// Trouver la route qui matche l'url fournie
		foreach ( $this->routes as $route ) {
			$varsValues = $route->match( $url );
			if ( $varsValues !== false AND $route->hasVars() ) // Si on a des variables, on doit les récupérer pour les faire transiter dans l'URL
			{
				$varsNames = $route->varsNames();
				$listVars  = array();
				foreach ( $varsValues as $key => $value ) // Récupérer les valeurs des attributs en clé-valeur entre $varsNames et $varsValues
				{
					if ( $key > 0 ) // Le premier retour de preg_match est la chaîne complète
					{
						$listVars[ $varsNames[ $key - 1 ] ] = $value;
					}
				}
				$route->setVars( $listVars );
				
				return $route;
			}
		}
		
		// On n'a pas trouvé : erreur
		throw new \RuntimeException( 'Couldn\'t find route ' . $url . ', no such route exists !', Router::ROUTE_NOT_FOUND );
	}
}