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
class Router extends ApplicationComponent {
	/**
	 * @var $routes Route[]
	 */
	protected $routes;
	const ROUTE_NOT_FOUND = 18;
	
	/**
	 * Construit le routeur comme un composant d'application
	 * Les routes sont créées à la construction.
	 *
	 * @param Application $App L'application dont le routeur gère les routes
	 */
	public function __construct(Application $App) {
		parent::__construct($App);
		$this->routes = array();
		// 1) Aller chercher dans la liste des routes toutes les routes existantes
		$xml = new \DOMDocument();
		$xml->load( __DIR__ . '/../../App/' . $this->app()->name() . '/Config/routes.xml' );
		$route_list = $xml->getElementsByTagName( 'route' );
		
		foreach ( $route_list as $route ) // Construire le routeur à partir de toutes les routes existantes
		{
			/**
			 * @var $route \DOMElement
			 */
			$vars = array();
			if ( $route->hasAttribute( 'vars' ) ) // Récupérer les arguments nécessaires à la route
			{
				$vars = explode( ',', $route->getAttribute( 'vars' ) );
			}
			// Ajouter la route au routeur les arguments passés
			$this->addRoute( new Route( array(
				'action'    => $route->getAttribute( 'action' ),
				'module'    => $route->getAttribute( 'module' ),
				'url'       => $route->getAttribute( 'url' ),
				'varsNames' => $vars,
			) ) );
		}
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
			}
			// Si c'est la bonne route, la renvoyer
			if ( $varsValues !== false ) {
				return $route;
			}
		}
		
		// On n'a pas trouvé : erreur
		throw new \RuntimeException( 'Couldn\'t find route ' . $url . ', no such route exists !', Router::ROUTE_NOT_FOUND );
	}
	
	/**
	 * Récupère une URL à partir du nom du module et de l'action souhaitée.
	 * Si l'URL à récupérer nécessite des paramètres, ils sont indiqués dans given_values_a.
	 *
	 * @param string $module Le module souhaité
	 * @param string $action L'action souhaitée
	 * @param array  $given_values_a Les variables nécessaires dans l'Url
	 *
	 * @return string L'URL calculée
	 */
	public function getUrlFromModuleAndAction($module, $action, $given_values_a = array() ) {
		// 1) Aller chercher dans la liste des routes toutes les routes existantes
		foreach ( $this->routes as $Route )
		{
			/**
			 * @var $Route Route
			 */
			if ($Route->module() === $module AND $Route->action() === $action) {
				// On a trouvé un module et une action qui correspondent : on vérifie si on a le bon nombre de paramètres
				$route_attribute_count = count($Route->varsNames());
				if (count($given_values_a) === $route_attribute_count) {
					// En plus elle a le bon nombre d'attributs
					// Prendre l'url
					$url = $Route->url();
					if (0 != $route_attribute_count) {
						// Rechercher les parties variables : elles sont indiquées par des parenthèses dans l'URL
						preg_match('/\(.+\)/', $url, $pattern_a);
						// Associer les clés des noms de variables aux parties variables
						$replacement_a = array_combine($Route->varsNames(), $pattern_a);
						foreach ($replacement_a as $var_name => $pattern) {
							// Si le pattern est respecté, alors on remplace l'élément correspondant dans l'URL
							// On vérifie d'abord s'il est bien set.
							if (!isset($given_values_a[$var_name])) {
								throw new \InvalidArgumentException('Le paramètre '.$var_name.' n\'est pas renseigné et est nécessaire au fonctionnement de la route.');
							}
							if (preg_match('/^'.$pattern.'$/', $given_values_a[$var_name])) {
								$url = preg_replace('/\(.+\)/', $given_values_a[$var_name], $url, 1);
							}
							else {
								throw new \InvalidArgumentException('Les paramètres de la route ne correspondent pas aux paramètres indiqués dans la configuration.');
							}
						}
					}
					// Remplacer les points échappées par des points et renvoyer l'URL calculée.
					return preg_replace('/\\\./', '.', $url);
				}
			}
		}
		// Si on n'a pas trouvé, c'est que la route est incorrecte
		throw new \InvalidArgumentException('Impossible de trouver l\'action '.$action.' dans le module '.$module.' de l\'application '.$this->app->name());
	}
	
}