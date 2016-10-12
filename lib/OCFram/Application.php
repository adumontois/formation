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
	 * @var $Routers_a Router[] Tableau bidimensionnel qui recense tous les routeurs existants
	 */
	protected $Routers_a;
	
	/**
	 * Construit un objet application en initialisant httpRequest et httpResponse
	 */
	public function __construct() {
		$this->httpRequest  = new HTTPRequest( $this );
		$this->httpResponse = new HTTPResponse( $this );
		$this->name         = '';
		$this->user         = new User( $this );
		$this->config       = new Config( $this );
		$this->Routers_a    = [];
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
		// Si le routeur appelé n'est pas créé, on le crée.
		if ( !isset( $this->Routers_a[ $this->name ] ) ) {
			$application_class_name = 'App\\'.$this->name.'\\'.$this->name.'Application';
			$this->addRouter($Router = new Router(new $application_class_name()));
		}
		
		// 2) Une fois toutes les routes créées, essayer de router l'URL reçue
		try {
			/**
			 * @var $route \OCFram\Route
			 */
			$route = $Router->getRoute( $this->httpRequest()->requestURI() );
			
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
	 * @param string $app            Le nom de l'application où chercher la route.
	 * @param string $module         Le module souhaité
	 * @param string $action         L'action souhaitée
	 * @param array  $given_values_a Les variables nécessaires dans l'Url
	 *
	 * @return string L'URL calculée
	 */
	public function getUrlFromModuleAndAction( $app, $module, $action, $given_values_a = array() ) {
		// Sélection du routeur
		if ( !isset( $this->Routers_a[ $app ] ) ) {
			// Si le routeur n'existe pas, on le crée et on l'ajoute.
			$application_class_name = 'App\\'.$app.'\\'.$app.'Application';
			$this->addRouter(new Router(new $application_class_name()));
		}
		// Appeler la méthode du routeur correspondant à l'application
		return $this->Routers_a[ $app ]->getUrlFromModuleAndAction( $module, $action, $given_values_a );
	}
	
	public function addRouter(Router $Router) {
		if (!in_array($Router, $this->Routers_a)) {
			$this->Routers_a[$Router->app()->name()] = $Router;
		}
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