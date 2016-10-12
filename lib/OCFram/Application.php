<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 30/09/2016
 * Time: 16:02
 */

namespace OCFram;

/**
 * Class Application
 *
 * Classe modélisant une des applications du site.
 *
 * @package OCFram
 */
abstract class Application {
	/**
	 * @var $httprequest HTTPRequest Requête envoyée par le client
	 */
	protected $httpRequest;
	/**
	 * @var $httpResponse HTTPResponse Page renvoyée au client par le serveur
	 */
	protected $httpResponse;
	/**
	 * @var $name string Nom de l'application
	 */
	protected $name;
	/**
	 * @var $user User Objet donnant les attributs de la session client
	 */
	protected $user;
	/**
	 * @var $config Config Objet donnant les variables de configuration serveur
	 */
	protected $config;
	
	/**
	 * Construit un objet application en initialisant httpRequest et httpResponse
	 */
	public function __construct() {
		$this->httpRequest  = new HTTPRequest( $this );
		$this->httpResponse = new HTTPResponse( $this );
		$this->name         = '';
		$this->user         = new User( $this );
		$this->config       = new Config( $this );
	}
	
	/**
	 * Methode permettant de lancer une application
	 */
	abstract public function run();
	
	/**
	 * Récupère un contrôleur à partir d'une url demandée dans httpRequest
	 *
	 * @return \OCFram\BackController
	 */
	public function getController() {
		$router = new Router();
		// 1) Aller chercher dans la liste des routes toutes les routes existantes
		$xml = new \DOMDocument();
		$xml->load( __DIR__ . '/../../App/' . $this->name() . '/Config/routes.xml' );
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
			$router->addRoute( new Route( array(
				'action'    => $route->getAttribute( 'action' ),
				'module'    => $route->getAttribute( 'module' ),
				'url'       => $route->getAttribute( 'url' ),
				'varsNames' => $vars,
			) ) );
		}
		
		// 2) Une fois toutes les routes créées, essayer de router l'URL reçue
		try {
			/**
			 * @var $route \OCFram\Route
			 */
			$route = $router->getRoute( $this->httpRequest()->requestURI() );
			
			// 3) Ajouter les variables lues dans l'url au tableau _GET
			// En effet ce sont des variables récupérées par l'url
			$_GET = array_merge( $_GET, $route->vars() );
			
			// 4) Instanciation du contrôleur
			$controllerClass = 'App\\' . $this->name . '\\Modules\\' . $route->module() . '\\' . $route->module() . 'Controller';
			
			return new $controllerClass( $this, $route->module(), $route->action() );
		}
		catch ( \RuntimeException $e ) {
			if ( $e->getCode() == Router::ROUTE_NOT_FOUND ) // Si on n'a pas trouvé la route, erreur 404
			{
				$this->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, $e );
			}
		}
		
		// Pas de retour ici
		return null;
	}
	
	/**
	 * Récupère une URL à partir du nom du module et de l'action souhaitée.
	 * Si l'URL à récupérer nécessite des paramètres, ils sont indiqués dans given_values_a.
	 *
	 * @param string $app    Le nom de l'application où chercher la route.
	 * @param string $module Le module souhaité
	 * @param string $action L'action souhaitée
	 * @param array  $given_values_a Les variables nécessaires dans l'Url
	 *
	 * @return string L'URL calculée
	 */
	static public function getUrlFromModuleAndAction( $app, $module, $action, $given_values_a = array() ) {
		// 1) Aller chercher dans la liste des routes toutes les routes existantes
		$Xml = new \DOMDocument();
		$Xml->load( __DIR__ . '/../../App/' . $app . '/Config/routes.xml' );
		$Route_a = $Xml->getElementsByTagName( 'route' );
		foreach ( $Route_a as $Route ) // Construire le routeur à partir de toutes les routes existantes
		{
			/**
			 * @var $Route \DOMElement
			 */
			if ($Route->getAttribute('module') === $module AND $Route->getAttribute('action') === $action) {
				// On a trouvé un module et une action qui correspondent : on récupère les noms de variables
				$route_attribute_count = 0;
				if ($Route->hasAttribute('vars')) {
					$var_names_a = explode(',', $Route->getAttribute('vars'));
					$route_attribute_count = count($var_names_a);
				}
				if (count($given_values_a) === $route_attribute_count) {
					// En plus elle a le bon nombre d'attributs
					// Prendre l'url
					$url = $Route->getAttribute('url');
					if (!empty($var_names_a)) {
						// Rechercher les parties variables : elles sont indiquées par des parenthèses dans l'URL
						preg_match('/\(.+\)/', $url, $pattern_a);
						// Associer les clés des noms de variables aux parties variables
						$replacement_a = array_combine($var_names_a, $pattern_a);
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
		throw new \InvalidArgumentException('Impossible de trouver l\'action '.$action.' dans le module '.$module.' de l\'application '.$app);
	}
	
	/**
	 * @return HTTPRequest
	 */
	public function httpRequest() {
		return $this->httpRequest;
	}
	
	/**
	 * @return HTTPResponse
	 */
	public function httpResponse() {
		return $this->httpResponse;
	}
	
	/**
	 * @return string
	 */
	public function name() {
		return $this->name;
	}
	
	/**
	 * @return User
	 */
	public function user() {
		return $this->user;
	}
	
	/**
	 * @return Config
	 */
	public function config() {
		return $this->config;
	}
}