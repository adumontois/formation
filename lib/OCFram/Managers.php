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
	 *
	 * @param $module string nom du module
	 *
	 * @return Manager
	 */
	public function getManagerOf( $module ) {
		if ( !is_string( $module ) OR empty( $module ) ) {
			throw new \InvalidArgumentException( 'Module must be a valid string' );
		}
		
		if ( !isset( $this->managers[ $module ] ) ) {
			$manager                   = '\\Model\\' . $module . 'Manager' . $this->api;
			$this->managers[ $module ] = new $manager( $this->dao );
		}
		
		return $this->managers[ $module ];
	}
}