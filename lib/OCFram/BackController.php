<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 09:56
 */

namespace OCFram;

abstract class BackController extends ApplicationComponent {
	protected $action;
	protected $module;
	protected $page;
	protected $view;
	protected $managers;
	
	/**
	 * Construit un backController comme une composant de l'application.
	 * Le backController est associé à une action et un module, et construit une vue.
	 *
	 * @param Application $app
	 * @param             $module
	 * @param             $action
	 */
	public function __construct( Application $app, $module, $action ) {
		parent::__construct( $app );
		$this->setAction( $action );
		$this->setModule( $module );
		$this->page = new Page( $app );
		$this->setView( $action );
		
		$this->managers = new Managers( 'PDO', PDOFactory::getMysqlConnexion() );
	}
	
	/**
	 * Exécute l'action associée au contrôleur.
	 */
	public function execute() {
		$method = 'execute' . ucfirst( $this->action );
		if ( !is_callable( [
			$this,
			$method,
		] )
		) {
			throw new \RuntimeException( 'Undefined action "' . $this->action . '" on this module' );
		}
		$this->$method( $this->app->httpRequest() );
	}
	
	/**
	 * @return Page
	 */
	public function page() {
		return $this->page;
	}
	
	/**
	 * Setter pour l'attribut module.
	 *
	 * @param $module string
	 */
	public function setModule( $module ) {
		if ( !is_string( $module ) || empty( $module ) ) {
			throw new \InvalidArgumentException( 'Module must be a valid string' );
		}
		$this->module = $module;
	}
	
	/**
	 * Setter pour l'attribut action.
	 *
	 * @param $action string
	 */
	public function setAction( $action ) {
		if ( !is_string( $action ) || empty( $action ) ) {
			throw new \InvalidArgumentException( 'Action must be a valid string' );
		}
		$this->action = $action;
	}
	
	/**
	 * Setter pour l'attribut view.
	 * Il définit le fichier de vue.
	 *
	 * @param $view string
	 */
	public function setView( $view ) {
		if ( !is_string( $view ) || empty( $view ) ) {
			throw new \InvalidArgumentException( 'View must be a valid string' );
		}
		$this->view = $view;
		$this->page->setContentFile( __DIR__ . '/../../App/' . $this->app->name() . '/Modules/' . $this->module . '/Views/' . $this->view . '.php' );
	}
}