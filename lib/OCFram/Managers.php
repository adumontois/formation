<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 10:27
 */

namespace OCFram;

/**
 * Class Managers
 * Rassemble tous les managers créés pour un Data Access Object.
 *
 * @package OCFram
 */
class Managers {
	/**
	 * @var $api string Class name of the Api
	 */
	protected $api;
	/**
	 * @var $dao mixed Data Access Object from the Api
	 */
	protected $dao;
	/**
	 * @var $managers Manager[]
	 */
	protected $managers;
	// For use of debug_backtrace to get class caller name
	const CALLER_LEVEL = 1;
	
	/**
	 * Construit un objet d'accès à la DB.
	 *
	 * @param $api string Class name of the Api
	 * @param $dao mixed Data Access Object from the Api
	 */
	public function __construct( $api, $dao ) {
		$this->api      = $api;
		$this->dao      = $dao;
		$this->managers = array();
	}
	
	/**
	 * Construit un manager à partir du module passé s'il n'est pas encore créé.
	 * Si le module n'est pas fourni, le module est déterminé comme le nom de l'entité associée à la classe appelante.
	 *
	 * @param $module string nom du module
	 *
	 * @return Manager
	 */
	public function getManagerOf( $module = null ) {
		// Récupérer l'entité associée à la classe appelante si on ne l'a pas déclaré précisément.
		if ( null === $module ) {
			$caller = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 2 )[ 1 ][ 'class' ];
			// Récupérer le nom de l'entité
			$module = substr( $caller, 0, strrpos( $caller, 'Controller' ) );
			$module = substr( $module, strrpos( $module, '\\' ) + 1 );
		}
		if ( !is_string( $module ) OR empty( $module ) ) {
			throw new \InvalidArgumentException( 'Module must be a valid string' );
		}
		
		// Construire le chemin relatif depuis le dossier vendors
		$module = 'Model\\' . $module;
		
		if ( !isset( $this->managers[ $module ] ) ) {
			$manager                   = $module . 'Manager' . $this->api;
			$this->managers[ $module ] = new $manager( $this->dao );
		}
		
		return $this->managers[ $module ];
	}
}