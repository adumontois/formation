<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 30/09/2016
 * Time: 16:02
 */

namespace OCFram;

abstract class Application {
	protected $httpRequest;
	protected $httpResponse;
	protected $name;
	protected $user;
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
			$router->addRoute( new Route( $route->getAttribute( 'action' ), $route->getAttribute( 'module' ), $route->getAttribute( 'url' ), $vars ) );
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
				$this->httpResponse()->redirect404();
			}
		}
		
		// Pas de retour ici
		return null;
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